<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Student;
use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $query = Transaction::with(['student', 'book'])->latest();

        if ($status === 'borrowed') {
            $query->whereNull('returned_at');
        } elseif ($status === 'returned') {
            $query->whereNotNull('returned_at');
        }

        $transactions = $query->paginate(15)->withQueryString();

        return view('transactions.index', compact('transactions', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::orderBy('name')->get();
        $books = Book::where('status', 'available')->orderBy('title')->get();

        return view('transactions.form', compact('students', 'books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
        ], [], [
            'student_id' => 'Siswa',
            'book_id' => 'Buku',
        ]);

        $book = Book::findOrFail($request->book_id);

        if ($book->status !== 'available') {
            return back()->withErrors(['book_id' => 'Buku ini sedang dipinjam oleh orang lain.']);
        }

        Transaction::create([
            'student_id' => $request->student_id,
            'book_id' => $request->book_id,
            'type' => 'borrow',
            'borrowed_at' => now(),
            'returned_at' => null,
            'jumlah_denda' => 0,
        ]);

        $book->update(['status' => 'borrowed']);

        Cache::forget('dashboard_metrics');

        return redirect()->route('transactions.index')->with('success', 'Transaksi peminjaman manual berhasil disimpan.');
    }

    /**
     * Manually mark a transaction as returned.
     */
    public function returnBook(Transaction $transaction)
    {
        if ($transaction->returned_at !== null) {
            return redirect()->route('transactions.index')->with('error', 'Buku ini sudah dikembalikan sebelumnya.');
        }

        $now = now();
        $jumlahDenda = 0;
        $borrowedAt = $transaction->borrowed_at;
        
        // Cek denda keterlambatan (Rp500/hari di hari kerja setelah 7 hari gratis)
        $totalDays = $borrowedAt->diffInDays($now);
        if ($totalDays > 7) {
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

        $transaction->book->update(['status' => 'available']);

        Cache::forget('dashboard_metrics');

        return redirect()->route('transactions.index')->with('success', 'Buku berhasil dikembalikan secara manual. Denda: Rp ' . number_format($jumlahDenda, 0, ',', '.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        // Jika statusnya masih dipinjam, kembalikan status bukunya dulu
        if ($transaction->returned_at === null) {
            $transaction->book->update(['status' => 'available']);
        }

        $transaction->delete();

        Cache::forget('dashboard_metrics');

        return redirect()->route('transactions.index')->with('success', 'Catatan transaksi berhasil dihapus.');
    }
}
