<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Student;
use App\Models\Book;
use App\Models\Transaction;

class RfidController extends Controller
{
    /**
     * Handle incoming RFID Scan payload (legacy route).
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

        Cache::put('last_scanned_uid', $uid, 30);

        $student = Student::where('rfid_uid', $uid)->first();
        if ($student) {
            $cached = Cache::get('active_student_session');

            if ($cached && is_array($cached) && isset($cached['id']) && $cached['id'] === $student->id) {
                Cache::forget('active_student_session');
                return response()->json([
                    'status' => 'session_closed',
                    'line1' => 'Sesi Ditutup',
                    'line2' => 'Silakan Tap Kartu'
                ]);
            } else {
                $studentData = $student->toArray();
                $studentData['timestamp'] = now()->timestamp * 1000;
                Cache::put('active_student_session', $studentData, 60);
                return response()->json([
                    'status' => 'session_active',
                    'line1' => 'Halo, ' . $student->name . '!',
                    'line2' => 'Silakan tap buku'
                ]);
            }
        }

        $book = Book::where('rfid_uid', $uid)->first();
        if ($book) {
            $activeStudent = Cache::get('active_student_session');

            if (!$activeStudent || !is_array($activeStudent) || !isset($activeStudent['id'])) {
                return response()->json([
                    'status' => 'error',
                    'line1' => 'DITOLAK!',
                    'line2' => 'Tap Kartu Dulu'
                ]);
            }

            if ($book->status === 'available') {
                Transaction::create([
                    'student_id'  => $activeStudent['id'],
                    'book_id'     => $book->id,
                    'type'        => 'borrow',
                    'borrowed_at' => now(),
                    'returned_at' => null,
                ]);
                $book->update(['status' => 'borrowed']);
                Cache::forget('dashboard_metrics');
                Cache::forget('active_student_session');
                return response()->json([
                    'status' => 'success',
                    'line1' => 'Pinjam Berhasil!',
                    'line2' => $book->title
                ]);
            } else {
                $transaction = Transaction::where('book_id', $book->id)
                    ->whereNull('returned_at')
                    ->latest()
                    ->first();

                $jumlahDenda = 0;
                $now = now();

                if ($transaction) {
                    $borrowedAt = $transaction->borrowed_at;
                    $totalDays  = $borrowedAt->diffInDays($now);
                    if ($totalDays > 7) {
                        $weekdays  = $borrowedAt->diffInWeekdays($now);
                        $hariTelat = $weekdays - 5;
                        if ($hariTelat > 0) {
                            $jumlahDenda = $hariTelat * 500;
                        }
                    }
                    $transaction->update([
                        'returned_at'  => $now,
                        'type'         => 'return',
                        'jumlah_denda' => $jumlahDenda,
                    ]);
                }

                $book->update(['status' => 'available']);
                Cache::forget('dashboard_metrics');
                Cache::forget('active_student_session');

                if ($jumlahDenda > 0) {
                    return response()->json([
                        'status' => 'success',
                        'line1'  => 'DIKEMBALIKAN',
                        'line2'  => 'Terlambat',
                        'line3'  => 'Denda: Rp ' . $jumlahDenda
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'line1'  => 'Kembali Sukses!',
                    'line2'  => $book->title
                ]);
            }
        }

        return response()->json([
            'status' => 'error',
            'line1'  => 'AKSES DITOLAK',
            'line2'  => 'Kartu Tidak Dikenal'
        ]);
    }

    /**
     * GET /api/session-status
     * Cek apakah ada sesi siswa yang aktif di cache.
     */
    public function getSessionStatus()
    {
        $activeSession = Cache::get('active_student_session');

        // Cek timeout sesi (60 detik) menggunakan timestamp yang tersimpan di cache
        if ($activeSession && isset($activeSession['timestamp'])) {
            $timeDiff = (now()->timestamp * 1000) - $activeSession['timestamp'];
            if ($timeDiff > 60000) {
                Cache::forget('active_student_session');
                $activeSession = null;
            }
        }

        if (!$activeSession) {
            return response()->json([
                'command' => 'RESET_STANDBY',
                'status'  => 'timeout'
            ]);
        }

        return response()->json([
            'status'  => 'active',
            'student' => $activeSession
        ]);
    }

    /**
     * GET /api/last-scan
     */
    public function getLastScan()
    {
        return response()->json([
            'uid' => Cache::get('last_scanned_uid')
        ]);
    }

    /**
     * POST /api/clear-scan
     */
    public function clearLastScan()
    {
        Cache::forget('last_scanned_uid');
        return response()->json(['status' => 'success']);
    }

    /**
     * Handle direct RFID Scan payload from ESP32.
     * POST /api/rfid/tap
     */
    public function handleTap(Request $request)
    {
        $uid = $request->input('uid');

        if (!$uid) {
            return response()->json([
                'status' => 'error',
                'line1'  => 'Input UID',
                'line2'  => 'Kosong'
            ], 400);
        }

        // Simpan UID ke cache untuk fitur auto-populate di form registrasi (30 detik)
        Cache::put('last_scanned_uid', $uid, 30);

        // 1. Cek apakah UID terdaftar sebagai Siswa
        $student = Student::where('rfid_uid', $uid)->first();
        if ($student) {
            $activeSession = Cache::get('active_student_session');

            if ($activeSession && is_array($activeSession) && isset($activeSession['id']) && $activeSession['id'] === $student->id) {
                // Sesi sama di-tap lagi -> Cek cooldown 5 detik (mencegah double-tap)
                $timeSinceOpen = (now()->timestamp * 1000) - ($activeSession['timestamp'] ?? 0);
                if ($timeSinceOpen < 5000) {
                    return response()->json([
                        'status' => 'session_active',
                        'line1'  => 'Halo, ' . substr($student->name, 0, 16) . '!',
                        'line2'  => 'Silakan tap buku'
                    ]);
                }

                // Tap lagi setelah cooldown -> Tutup Sesi
                Cache::forget('active_student_session');
                return response()->json([
                    'status' => 'session_closed',
                    'line1'  => 'Sesi Ditutup',
                    'line2'  => 'Silakan Tap Kartu'
                ]);
            } else {
                // Buka sesi baru untuk student ini (durasi 60 detik)
                $studentData = $student->toArray();
                $studentData['timestamp'] = now()->timestamp * 1000;
                Cache::put('active_student_session', $studentData, 60);

                return response()->json([
                    'status' => 'session_active',
                    'line1'  => 'Halo, ' . substr($student->name, 0, 16) . '!',
                    'line2'  => 'Silakan tap buku'
                ]);
            }
        }

        // 2. Cek apakah UID terdaftar sebagai Buku
        $book = Book::where('rfid_uid', $uid)->first();
        if ($book) {
            $activeStudent = Cache::get('active_student_session');

            // Tidak ada sesi aktif
            if (!$activeStudent || !is_array($activeStudent) || !isset($activeStudent['id'])) {
                return response()->json([
                    'status' => 'error',
                    'line1'  => 'DITOLAK!',
                    'line2'  => 'Tap Kartu Dulu'
                ]);
            }

            // Sesi expired (lebih dari 60 detik)
            if (isset($activeStudent['timestamp'])) {
                $timeDiff = (now()->timestamp * 1000) - $activeStudent['timestamp'];
                if ($timeDiff > 60000) {
                    Cache::forget('active_student_session');
                    return response()->json([
                        'status' => 'error',
                        'line1'  => 'SESI EXPIRED!',
                        'line2'  => 'Silakan Tap Lagi'
                    ]);
                }
            }

            $line1 = '';
            $line2 = substr($book->title, 0, 16);
            $line3 = '';

            if ($book->status === 'available') {
                // Pinjam Buku
                Transaction::create([
                    'student_id'  => $activeStudent['id'],
                    'book_id'     => $book->id,
                    'type'        => 'borrow',
                    'borrowed_at' => now(),
                    'returned_at' => null,
                ]);
                $book->update(['status' => 'borrowed']);
                $line1 = 'Pinjam Berhasil!';
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
                    $totalDays  = $borrowedAt->diffInDays($now);
                    if ($totalDays > 7) {
                        $weekdays  = $borrowedAt->diffInWeekdays($now);
                        $hariTelat = $weekdays - 5;
                        if ($hariTelat > 0) {
                            $jumlahDenda = $hariTelat * 500;
                        }
                    }
                    $transaction->update([
                        'returned_at'  => $now,
                        'type'         => 'return',
                        'jumlah_denda' => $jumlahDenda,
                    ]);
                }

                $book->update(['status' => 'available']);
                $line1 = 'Kembali Sukses!';
                if ($jumlahDenda > 0) {
                    $line3 = 'Denda: Rp ' . $jumlahDenda;
                }
            }

            // Hapus sesi dan cache dashboard setelah transaksi sukses
            Cache::forget('active_student_session');
            Cache::forget('dashboard_metrics');

            return response()->json([
                'status' => 'success',
                'line1'  => $line1,
                'line2'  => $line2,
                'line3'  => $line3
            ]);
        }

        // 3. Jika tidak terdaftar sama sekali
        return response()->json([
            'status' => 'error',
            'line1'  => 'AKSES DITOLAK',
            'line2'  => 'Kartu Tidak Dikenal'
        ]);
    }
}
