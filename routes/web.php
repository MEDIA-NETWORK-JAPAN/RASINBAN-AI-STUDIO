<?php

use App\Http\Controllers\TwoFactorChallengeController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

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
    Volt::route('/dashboard', 'user.dashboard')->name('dashboard');
});

// Admin area (authenticated + admin only)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    \App\Http\Middleware\AdminGuard::class,
])->prefix('admin')->group(function () {
    Volt::route('/', 'admin.dashboard')->name('admin.dashboard');
    Volt::route('/teams', 'admin.team-list')->name('admin.teams');
    Volt::route('/teams/import', 'admin.csv-import')->name('admin.teams.import');
    Volt::route('/teams/{team}', 'admin.team-edit')->name('admin.teams.edit');
    Volt::route('/apps', 'admin.dify-app-list')->name('admin.apps');
    Volt::route('/apps/{difyApp}', 'admin.dify-app-edit')->name('admin.apps.edit');
    Volt::route('/usages', 'admin.usage-list')->name('admin.usages');
    Volt::route('/dr/export', 'admin.dr-export')->name('admin.dr.export');
});
