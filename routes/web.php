<?php

use App\Livewire\Auth\CashierLogin;
use App\Livewire\Auth\Login;
use App\Livewire\AdminDashboard;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Admin\ProductManagement;
use App\Livewire\Admin\CategoryManagement;
use App\Livewire\Pos\SessionManager;
use App\Livewire\Pos\Terminal;
use App\Livewire\Reports\Dashboard as ReportsDashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─── Guest Routes ────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/cashier-login', CashierLogin::class)->name('cashier.login');
});

// ─── Logout ──────────────────────────────────────────────────────────────────
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

// ─── Admin & Manager Backoffice ───────────────────────────────────────────────
Route::middleware(['auth', 'role:admin,manager'])->group(function () {

    // Dashboard
    Route::get('/', AdminDashboard::class)->name('dashboard');

    // Reports
    Route::get('/reports', ReportsDashboard::class)->name('reports');

    // Products (Admin only for create/edit, manager can view)
    Route::get('/products', ProductManagement::class)->name('products');
    Route::get('/categories', CategoryManagement::class)->name('categories');

    // Users (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', UserManagement::class)->name('users');
    });
});

// ─── POS Terminal (Cashier only) ──────────────────────────────────────────────
Route::middleware(['auth', 'role:cashier'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/session', SessionManager::class)->name('session');
    Route::get('/terminal', Terminal::class)->name('terminal');
});
