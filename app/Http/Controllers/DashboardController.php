<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Book;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Display the sirkulasi live dashboard.
     */
    public function index(Request $request)
    {
        $data = Cache::remember('dashboard_metrics', 2, function() {
            return [
                'total_students' => Student::count(),
                'total_books' => Book::count(),
                'available_books' => Book::where('status', 'available')->count(),
                'borrowed_books' => Book::where('status', 'borrowed')->count(),
                'total_fines' => Transaction::sum('jumlah_denda'),
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
                        'jumlah_denda' => $t->jumlah_denda,
                    ];
                })->toArray(),
            ];
        });

        // Active session is dynamic and retrieved from Firebase to be stateless (Vercel-ready)
        $firebaseUrl = rtrim(env('FIREBASE_DATABASE_URL'), '/');
        $activeSession = null;
        
        if (app()->environment() === 'local') {
            // Direct cache read for instant response locally
            $activeSession = Cache::get('active_student_session');
            
            // Check if local session has expired
            if ($activeSession && isset($activeSession['timestamp'])) {
                $timeDiff = (now()->timestamp * 1000) - $activeSession['timestamp'];
                if ($timeDiff > 60000) {
                    Cache::forget('active_student_session');
                    if ($firebaseUrl) {
                        try {
                            Http::put($firebaseUrl . '/active_session.json', null);
                        } catch (\Exception $e) {}
                    }
                    $activeSession = null;
                }
            }
        } else if ($firebaseUrl) {
            try {
                $activeSession = Http::get($firebaseUrl . '/active_session.json')->json();
                
                // Check if session timestamp is still within 60 seconds limit
                if ($activeSession && isset($activeSession['timestamp'])) {
                    $timeDiff = (now()->timestamp * 1000) - $activeSession['timestamp'];
                    if ($timeDiff > 60000) {
                        // Expired -> clean Firebase
                        Http::put($firebaseUrl . '/active_session.json', null);
                        $activeSession = null;
                    }
                }
            } catch (\Exception $e) {
                $activeSession = Cache::get('active_student_session');
            }
        } else {
            $activeSession = Cache::get('active_student_session');
        }

        $data['active_session'] = $activeSession;

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($data);
        }

        return view('dashboard', $data);
    }
}
