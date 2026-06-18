<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::latest()->get();
        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $book = new Book();
        return view('books.form', compact('book'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rfid_uid' => 'required|string|max:50|unique:books,rfid_uid',
            'title' => 'required|string|max:150',
            'author' => 'required|string|max:100',
        ]);

        $validated['status'] = 'available';

        Book::create($validated);
        Cache::forget('dashboard_metrics');

        return redirect()->route('books.index')->with('success', 'Data buku berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        return view('books.form', compact('book'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rfid_uid' => 'required|string|max:50|unique:books,rfid_uid,' . $book->id,
            'title' => 'required|string|max:150',
            'author' => 'required|string|max:100',
            'status' => 'required|string|in:available,borrowed',
        ]);

        $book->update($validated);
        Cache::forget('dashboard_metrics');

        return redirect()->route('books.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        Cache::forget('dashboard_metrics');
        return redirect()->route('books.index')->with('success', 'Data buku berhasil dihapus.');
    }

    /**
     * Export books to CSV.
     */
    public function export()
    {
        $books = Book::latest()->get();
        $filename = 'data_buku_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($books) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel
            fputs($file, "\xEF\xBB\xBF");
            
            // Add separator directive for Excel
            fputs($file, "sep=,\r\n");
            
            // Header columns
            fputcsv($file, ['No', 'Judul Buku', 'Pengarang', 'RFID UID', 'Status'], ',');

            foreach ($books as $index => $book) {
                fputcsv($file, [
                    $index + 1,
                    $book->title,
                    $book->author,
                    $book->rfid_uid,
                    $book->status === 'available' ? 'Tersedia' : 'Dipinjam',
                ], ',');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
