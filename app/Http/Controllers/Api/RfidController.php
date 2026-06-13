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

    /**
     * Get active session status.
     * GET /api/session-status
     */
    public function getSessionStatus()
    {
        if (!Cache::has('active_student_session')) {
            return response()->json([
                'command' => 'RESET_STANDBY',
                'status' => 'timeout'
            ]);
        }

        return response()->json([
            'status' => 'active',
            'student' => Cache::get('active_student_session')
        ]);
    }
}
