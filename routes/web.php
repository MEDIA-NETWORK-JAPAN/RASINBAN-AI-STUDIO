<?php

use App\Http\Controllers\TwoFactorChallengeController;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\User\Dashboard as UserDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

// Two-factor challenge (custom OTP, not Fortify built-in)
Route::middleware('web')->group(function () {
    Route::get('/two-factor-challenge', [TwoFactorChallengeController::class, 'show'])
        ->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [TwoFactorChallengeController::class, 'store']);
    Route::post('/two-factor-challenge/resend', [TwoFactorChallengeController::class, 'resend'])
        ->name('two-factor.resend');
});

// User area (authenticated, any user)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
});

// Admin area (authenticated + admin only)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    \App\Http\Middleware\AdminGuard::class,
])->prefix('admin')->group(function () {
    Route::get('/', AdminDashboard::class)->name('admin.dashboard');
});
