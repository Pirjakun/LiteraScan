<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RfidController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/rfid-scan', [RfidController::class, 'scan']);
Route::post('/rfid/tap', [RfidController::class, 'handleTap']);
Route::get('/session-status', [RfidController::class, 'getSessionStatus']);
Route::get('/last-scan', [RfidController::class, 'getLastScan']);
Route::post('/clear-scan', [RfidController::class, 'clearLastScan']);
