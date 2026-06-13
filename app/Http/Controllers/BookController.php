<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

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
            'status' => 'required|string|in:available,borrowed',
        ]);

        Book::create($validated);

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

        return redirect()->route('books.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Data buku berhasil dihapus.');
    }
}
