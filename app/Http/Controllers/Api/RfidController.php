<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\Student;
use App\Models\Book;
use App\Models\Transaction;

class RfidController extends Controller
{
    /**
     * Handle incoming RFID Scan payload.
     * POST /api/rfid-scan
     */
    public function scan(Request $request)
    {
        $uid = $request->input('uid');

        if (!$uid) {
            return response()->json([
                'status' => 'error',
                'line1' => 'Input UID',
                'line2' => 'Kosong'
            ], 400);
        }

        // Simpan UID ke cache untuk fitur auto-populate di form registrasi (30 detik)
        Cache::put('last_scanned_uid', $uid, 30);

        // 1. Cek apakah UID terdaftar di Students
        $student = Student::where('rfid_uid', $uid)->first();
        if ($student) {
            $cached = Cache::get('active_student_session');
            
            if ($cached && $cached['id'] === $student->id) {
                // Sesi sama di-tap lagi -> Hapus cache (Tutup Sesi)
                Cache::forget('active_student_session');
                return response()->json([
                    'status' => 'session_closed',
                    'line1' => 'Sesi Ditutup',
                    'line2' => 'Silakan Tap Kartu'
                ]);
            } else {
                // Buka sesi baru / update sesi untuk student ini (durasi 10 detik)
                Cache::put('active_student_session', $student->toArray(), 10);
                return response()->json([
                    'status' => 'session_active',
                    'line1' => 'Halo, ' . $student->name . '!',
                    'line2' => 'Silakan tap buku'
                ]);
            }
        }

        // 2. Cek apakah UID terdaftar di Books
        $book = Book::where('rfid_uid', $uid)->first();
        if ($book) {
            $activeStudent = Cache::get('active_student_session');

            if (!$activeStudent) {
                return response()->json([
                    'status' => 'error',
                    'line1' => 'DITOLAK!',
                    'line2' => 'Tap Kartu Dulu'
                ]);
            }

            if ($book->status === 'available') {
                // Pinjam Buku
                Transaction::create([
                    'student_id' => $activeStudent['id'],
                    'book_id' => $book->id,
                    'type' => 'borrow',
                    'borrowed_at' => now(),
                    'returned_at' => null,
                ]);

                $book->update(['status' => 'borrowed']);
                
                // Hapus cache dashboard agar data terupdate instan
                Cache::forget('dashboard_metrics');
                
                // Transaksi selesai -> Hapus cache sesi
                Cache::forget('active_student_session');

                return response()->json([
                    'status' => 'success',
                    'line1' => 'Pinjam Berhasil!',
                    'line2' => $book->title
                ]);
            } else {
                // Pengembalian Buku
                $transaction = Transaction::where('book_id', $book->id)
                    ->whereNull('returned_at')
                    ->latest()
                    ->first();

                $jumlahDenda = 0;
                $now = now();

                if ($transaction) {
                    $borrowedAt = $transaction->borrowed_at;
                    // Hitung total hari keterlambatan (termasuk hari libur)
                    $totalDays = $borrowedAt->diffInDays($now);
                    
                    if ($totalDays > 7) {
                        // Total hari kerja antara tanggal pinjam dan tanggal kembali
                        $weekdays = $borrowedAt->diffInWeekdays($now);
                        $hariTelat = $weekdays - 5; // dikurang 5 hari kerja gratis
                        if ($hariTelat > 0) {
                            $jumlahDenda = $hariTelat * 500;
                        }
                    }

                    $transaction->update([
                        'returned_at' => $now,
                        'type' => 'return',
                        'jumlah_denda' => $jumlahDenda,
                    ]);
                }

                $book->update(['status' => 'available']);
                
                // Hapus cache dashboard agar data terupdate instan
                Cache::forget('dashboard_metrics');
                
                // Transaksi selesai -> Hapus cache sesi
                Cache::forget('active_student_session');

                if ($jumlahDenda > 0) {
                    return response()->json([
                        'status' => 'success',
                        'line1' => 'DIKEMBALIKAN',
                        'line2' => 'Terlambat',
                        'line3' => 'Denda: Rp ' . $jumlahDenda
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'line1' => 'Kembali Sukses!',
                    'line2' => $book->title
                ]);
            }
        }

        // 3. Jika tidak terdaftar
        return response()->json([
            'status' => 'error',
            'line1' => 'AKSES DITOLAK',
            'line2' => 'Kartu Tidak Dikenal'
        ]);
    }

    public function getSessionStatus()
    {
        $activeSession = Cache::get('active_student_session');
        $firebaseUrl = rtrim(env('FIREBASE_DATABASE_URL'), '/');

        // Only query Firebase if not found in local cache and not in local development environment
        if (!$activeSession && app()->environment() !== 'local' && $firebaseUrl) {
            try {
                $activeSession = Http::get($firebaseUrl . '/active_session.json')->json();
                
                // Check if session has expired (60 seconds)
                if ($activeSession && isset($activeSession['timestamp'])) {
                    $timeDiff = (now()->timestamp * 1000) - $activeSession['timestamp'];
                    if ($timeDiff > 60000) {
                        Http::put($firebaseUrl . '/active_session.json', null);
                        Http::put($firebaseUrl . '/rfid_response.json', [
                            'command' => 'RESET_STANDBY',
                            'status' => 'timeout',
                            'timestamp' => now()->timestamp * 1000
                        ]);
                        $activeSession = null;
                    }
                }
            } catch (\Exception $e) {}
        }

        // Local timeout handler if session exists but is expired
        if ($activeSession && isset($activeSession['timestamp'])) {
            $timeDiff = (now()->timestamp * 1000) - $activeSession['timestamp'];
            if ($timeDiff > 60000) { // 60 seconds
                Cache::forget('active_student_session');
                if (app()->environment() === 'local' && $firebaseUrl) {
                    try {
                        Http::put($firebaseUrl . '/active_session.json', null);
                        Http::put($firebaseUrl . '/rfid_response.json', [
                            'command' => 'RESET_STANDBY',
                            'status' => 'timeout',
                            'timestamp' => now()->timestamp * 1000
                        ]);
                    } catch (\Exception $e) {}
                }
                $activeSession = null;
            }
        }

        if (!$activeSession) {
            return response()->json([
                'command' => 'RESET_STANDBY',
                'status' => 'timeout'
            ]);
        }

        return response()->json([
            'status' => 'active',
            'student' => $activeSession
        ]);
    }

    /**
     * Get the last scanned RFID UID from cache.
     * GET /api/last-scan
     */
    public function getLastScan()
    {
        return response()->json([
            'uid' => Cache::get('last_scanned_uid')
        ]);
    }

    /**
     * Clear the last scanned RFID UID from cache.
     * POST /api/clear-scan
     */
    public function clearLastScan()
    {
        Cache::forget('last_scanned_uid');
        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Handle direct RFID Scan payload from ESP32.
     * POST /api/rfid/tap
     */
    public function handleTap(Request $request)
    {
        $uid = $request->input('uid');
        $firebaseUrl = rtrim(env('FIREBASE_DATABASE_URL'), '/');

        if (!$uid) {
            return response()->json([
                'status' => 'error',
                'line1' => 'Input UID',
                'line2' => 'Kosong'
            ], 400);
        }

        // Simpan UID ke cache untuk fitur auto-populate di form registrasi (30 detik)
        Cache::put('last_scanned_uid', $uid, 30);

        // 1. Cek apakah UID terdaftar sebagai Siswa
        $student = Student::where('rfid_uid', $uid)->first();
        if ($student) {
            // Kita baca sesi aktif saat ini dari local cache dulu, fallback ke Firebase jika kosong (hanya di non-local environment)
            $activeSession = Cache::get('active_student_session');
            if (!$activeSession && app()->environment() !== 'local' && $firebaseUrl) {
                try {
                    $fetched = Http::timeout(1.0)->get($firebaseUrl . '/active_session.json')->json();
                    // Pastikan data dari Firebase adalah array yang valid sebelum dipakai
                    if (is_array($fetched) && isset($fetched['id'])) {
                        $activeSession = $fetched;
                    }
                } catch (\Exception $e) {}
            }

            if ($activeSession && is_array($activeSession) && isset($activeSession['id']) && $activeSession['id'] === $student->id) {
                // Sesi sama di-tap lagi -> Cek apakah sudah lewat cooldown (mencegah double-tap)
                $timeSinceOpen = (now()->timestamp * 1000) - $activeSession['timestamp'];
                if ($timeSinceOpen < 5000) { // Cooldown 5 detik
                    return response()->json([
                        'status' => 'session_active',
                        'line1' => 'Halo, ' . substr($student->name, 0, 16) . '!',
                        'line2' => 'Silakan tap buku'
                    ]);
                }

                // Sesi sama di-tap lagi setelah melewati cooldown -> Hapus sesi (Tutup Sesi)
                Cache::forget('active_student_session');
                if ($firebaseUrl) {
                    dispatch(function () use ($firebaseUrl) {
                        try {
                            Http::timeout(1.5)->put($firebaseUrl . '/active_session.json', null);
                            Http::timeout(1.5)->put($firebaseUrl . '/rfid_response.json', [
                                'status' => 'session_closed',
                                'line1' => 'Sesi Ditutup',
                                'line2' => 'Silakan Tap Kartu',
                                'timestamp' => now()->timestamp * 1000
                            ]);
                        } catch (\Exception $e) {}
                    })->afterResponse();
                }

                return response()->json([
                    'status' => 'session_closed',
                    'line1' => 'Sesi Ditutup',
                    'line2' => 'Silakan Tap Kartu'
                ]);
            } else {
                // Buka sesi baru untuk student ini
                $studentData = $student->toArray();
                $studentData['timestamp'] = now()->timestamp * 1000;
                Cache::put('active_student_session', $studentData, 60); // Durasi sesi 60 detik

                if ($firebaseUrl) {
                    $studentName = $student->name;
                    dispatch(function () use ($firebaseUrl, $studentData, $studentName) {
                        try {
                            Http::timeout(1.5)->put($firebaseUrl . '/active_session.json', $studentData);
                            Http::timeout(1.5)->put($firebaseUrl . '/rfid_response.json', [
                                'status' => 'session_active',
                                'line1' => 'Halo, ' . substr($studentName, 0, 16) . '!',
                                'line2' => 'Silakan tap buku',
                                'timestamp' => now()->timestamp * 1000
                            ]);
                        } catch (\Exception $e) {}
                    })->afterResponse();
                }

                return response()->json([
                    'status' => 'session_active',
                    'line1' => 'Halo, ' . substr($student->name, 0, 16) . '!',
                    'line2' => 'Silakan tap buku'
                ]);
            }
        }

        // 2. Cek apakah UID terdaftar sebagai Buku
        $book = Book::where('rfid_uid', $uid)->first();
        if ($book) {
            // Kita baca sesi aktif saat ini dari local cache dulu, fallback ke Firebase jika kosong (hanya di non-local environment)
            $activeStudent = Cache::get('active_student_session');
            if (!$activeStudent && app()->environment() !== 'local' && $firebaseUrl) {
                try {
                    $fetched = Http::timeout(1.0)->get($firebaseUrl . '/active_session.json')->json();
                    // Pastikan data dari Firebase adalah array yang valid sebelum dipakai
                    if (is_array($fetched) && isset($fetched['id'])) {
                        $activeStudent = $fetched;
                    }
                } catch (\Exception $e) {}
            }

            if (!$activeStudent || !is_array($activeStudent) || !isset($activeStudent['id'])) {
                if ($firebaseUrl) {
                    dispatch(function () use ($firebaseUrl) {
                        try {
                            Http::timeout(1.5)->put($firebaseUrl . '/rfid_response.json', [
                                'status' => 'error',
                                'line1' => 'DITOLAK!',
                                'line2' => 'Tap Kartu Dulu',
                                'timestamp' => now()->timestamp * 1000
                            ]);
                        } catch (\Exception $e) {}
                    })->afterResponse();
                }

                return response()->json([
                    'status' => 'error',
                    'line1' => 'DITOLAK!',
                    'line2' => 'Tap Kartu Dulu'
                ]);
            }

            // Validasi apakah sesi siswa di Firebase sudah expired (misal lebih dari 60 detik)
            $sessionExpired = false;
            if (isset($activeStudent['timestamp'])) {
                $timeDiff = (now()->timestamp * 1000) - $activeStudent['timestamp'];
                if ($timeDiff > 60000) { // 60 detik
                    $sessionExpired = true;
                }
            }

            if ($sessionExpired) {
                // Sesi expired -> Hapus
                Cache::forget('active_student_session');
                if ($firebaseUrl) {
                    dispatch(function () use ($firebaseUrl) {
                        try {
                            Http::timeout(1.5)->put($firebaseUrl . '/active_session.json', null);
                            Http::timeout(1.5)->put($firebaseUrl . '/rfid_response.json', [
                                'status' => 'error',
                                'line1' => 'SESI EXPIRED!',
                                'line2' => 'Silakan Tap Lagi',
                                'timestamp' => now()->timestamp * 1000
                            ]);
                        } catch (\Exception $e) {}
                    })->afterResponse();
                }

                return response()->json([
                    'status' => 'error',
                    'line1' => 'SESI EXPIRED!',
                    'line2' => 'Silakan Tap Lagi'
                ]);
            }

            $line1 = '';
            $line2 = substr($book->title, 0, 16);
            $line3 = '';

            if ($book->status === 'available') {
                // Pinjam Buku
                Transaction::create([
                    'student_id' => $activeStudent['id'],
                    'book_id' => $book->id,
                    'type' => 'borrow',
                    'borrowed_at' => now(),
                    'returned_at' => null,
                ]);

                $book->update(['status' => 'borrowed']);
                $line1 = 'Pinjam Berhasil!';
                $typeText = 'borrow';
            } else {
                // Pengembalian Buku
                $transaction = Transaction::where('book_id', $book->id)
                    ->whereNull('returned_at')
                    ->latest()
                    ->first();

                $jumlahDenda = 0;
                $now = now();

                if ($transaction) {
                    $borrowedAt = $transaction->borrowed_at;
                    $totalDays = $borrowedAt->diffInDays($now);
                    
                    if ($totalDays > 7) {
                        $weekdays = $borrowedAt->diffInWeekdays($now);
                        $hariTelat = $weekdays - 5;
                        if ($hariTelat > 0) {
                            $jumlahDenda = $hariTelat * 500;
                        }
                    }

                    $transaction->update([
                        'returned_at' => $now,
                        'type' => 'return',
                        'jumlah_denda' => $jumlahDenda,
                    ]);
                }

                $book->update(['status' => 'available']);
                $line1 = 'Kembali Sukses!';
                $typeText = 'return';
                if ($jumlahDenda > 0) {
                    $line3 = 'Denda: Rp ' . $jumlahDenda;
                }
            }

            // Hapus sesi setelah transaksi sukses
            Cache::forget('active_student_session');

            // Hapus cache dashboard agar data terupdate instan
            Cache::forget('dashboard_metrics');

            if ($firebaseUrl) {
                $studentName = $activeStudent['name'];
                $bookTitle = $book->title;
                dispatch(function () use ($firebaseUrl, $line1, $line2, $line3, $typeText, $studentName, $bookTitle) {
                    try {
                        // Update sesi aktif menjadi null di Firebase
                        Http::timeout(1.5)->put($firebaseUrl . '/active_session.json', null);

                        // Update Firebase dengan respon transaksi sukses
                        Http::timeout(1.5)->put($firebaseUrl . '/rfid_response.json', [
                            'status' => 'success',
                            'line1' => $line1,
                            'line2' => $line2,
                            'line3' => $line3,
                            'timestamp' => now()->timestamp * 1000
                        ]);

                        // Kirim sinyal update transaksi agar dashboard ter-refresh otomatis
                        Http::timeout(1.5)->put($firebaseUrl . '/last_transaction.json', [
                            'status' => 'success',
                            'type' => $typeText,
                            'student_name' => $studentName,
                            'book_title' => $bookTitle,
                            'timestamp' => now()->timestamp * 1000
                        ]);
                    } catch (\Exception $e) {}
                })->afterResponse();
            }

            return response()->json([
                'status' => 'success',
                'line1' => $line1,
                'line2' => $line2,
                'line3' => $line3
            ]);
        }

        // 3. Jika tidak terdaftar
        if ($firebaseUrl) {
            dispatch(function () use ($firebaseUrl) {
                try {
                    Http::timeout(1.5)->put($firebaseUrl . '/rfid_response.json', [
                        'status' => 'error',
                        'line1' => 'AKSES DITOLAK',
                        'line2' => 'Kartu Tidak Dikenal',
                        'timestamp' => now()->timestamp * 1000
                    ]);
                } catch (\Exception $e) {}
            })->afterResponse();
        }

        return response()->json([
            'status' => 'error',
            'line1' => 'AKSES DITOLAK',
            'line2' => 'Kartu Tidak Dikenal'
        ]);
    }
}
