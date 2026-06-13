<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\TransactionController;

Route::get('/', [DashboardController::class, 'index']);
Route::resource('students', StudentController::class);
Route::resource('books', BookController::class);

Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::post('transactions/{transaction}/return', [TransactionController::class, 'returnBook'])->name('transactions.return');
Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
