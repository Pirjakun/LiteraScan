<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Book;
use App\Models\Transaction;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Display the sirkulasi live dashboard.
     */
    public function index(Request $request)
    {
        $data = [
            'total_students' => Student::count(),
            'total_books' => Book::count(),
            'available_books' => Book::where('status', 'available')->count(),
            'borrowed_books' => Book::where('status', 'borrowed')->count(),
            'recent_transactions' => Transaction::with(['student', 'book'])->latest()->take(10)->get()->map(function($t) {
                return [
                    'id' => $t->id,
                    'student_name' => $t->student ? $t->student->name : 'N/A',
                    'student_nim' => $t->student ? $t->student->nim : 'N/A',
                    'book_title' => $t->book ? $t->book->title : 'N/A',
                    'type' => $t->type,
                    'borrowed_at' => $t->borrowed_at ? $t->borrowed_at->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') : null,
                    'returned_at' => $t->returned_at ? $t->returned_at->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s') : null,
                    'borrowed_human' => $t->borrowed_at ? $t->borrowed_at->diffForHumans() : null,
                    'returned_human' => $t->returned_at ? $t->returned_at->diffForHumans() : null,
                ];
            }),
            'active_session' => Cache::get('active_student_session'),
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($data);
        }

        return view('dashboard', $data);
    }
}
