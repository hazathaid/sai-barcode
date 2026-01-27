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

// Admin dashboard (simple route)
use App\Models\Event;
use App\Http\Controllers\AdminEventController;

Route::get('/admin', function(){
    $events = Event::orderByDesc('starts_at')->get();
    return view('admin.dashboard', compact('events'));
})->middleware('auth')->name('admin.dashboard');

// Admin events CRUD
Route::middleware('auth')->prefix('admin/events')->name('admin.events.')->group(function(){
    Route::get('/', [AdminEventController::class, 'index'])->name('index');
    Route::get('/create', [AdminEventController::class, 'create'])->name('create');
    Route::post('/', [AdminEventController::class, 'store'])->name('store');
    Route::get('/{event}/edit', [AdminEventController::class, 'edit'])->name('edit');
    Route::put('/{event}', [AdminEventController::class, 'update'])->name('update');
    Route::delete('/{event}', [AdminEventController::class, 'destroy'])->name('destroy');
});

// Admin event tickets list
use App\Http\Controllers\AdminTicketController;
Route::get('/admin/events/{event}/tickets', [AdminTicketController::class, 'index'])->middleware('auth')->name('admin.events.tickets');

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

