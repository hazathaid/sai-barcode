<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminCheckinController;

Route::post('/admin/events/{event}/checkin', [AdminCheckinController::class, 'checkin'])
    ->middleware('auth');
