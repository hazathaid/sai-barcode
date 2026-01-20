<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminScannerController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Public event pages and registration
Route::get('/e/{event:slug}', [EventController::class, 'show'])->name('events.show');
Route::post('/e/{event:slug}/register', [RegisterController::class, 'store'])->name('events.register');

// Ticket display by QR token
Route::get('/t/{token}', [TicketController::class, 'show'])->name('tickets.show');

// Admin scanner (requires authentication)
Route::get('/admin/events/{event}/scanner', [AdminScannerController::class, 'show'])
    ->middleware('auth')
    ->name('admin.events.scanner');

// Simple auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

