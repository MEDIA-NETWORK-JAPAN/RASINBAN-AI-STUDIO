<?php

use App\Http\Controllers\TwoFactorChallengeController;
use App\Livewire\Admin\CsvImport;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\DifyAppEdit;
use App\Livewire\Admin\DifyAppList;
use App\Livewire\Admin\DrExport;
use App\Livewire\Admin\TeamEdit;
use App\Livewire\Admin\TeamList;
use App\Livewire\Admin\UsageList;
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
    Route::get('/teams', TeamList::class)->name('admin.teams');
    Route::get('/teams/import', CsvImport::class)->name('admin.teams.import');
    Route::get('/teams/{team}', TeamEdit::class)->name('admin.teams.edit');
    Route::get('/apps', DifyAppList::class)->name('admin.apps');
    Route::get('/apps/{difyApp}', DifyAppEdit::class)->name('admin.apps.edit');
    Route::get('/usages', UsageList::class)->name('admin.usages');
    Route::get('/dr/export', DrExport::class)->name('admin.dr.export');
});
