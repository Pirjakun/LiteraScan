<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;

// Auth Routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Required Authentication)
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    Route::resource('students', StudentController::class);

    Route::get('books/export', [BookController::class, 'export'])->name('books.export');
    Route::resource('books', BookController::class);

    Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::post('transactions/{transaction}/return', [TransactionController::class, 'returnBook'])->name('transactions.return');
    Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
});
