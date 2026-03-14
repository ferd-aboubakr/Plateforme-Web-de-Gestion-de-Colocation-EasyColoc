<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// ── Page d'accueil ─────────────────────────────────────────
Route::get('/', fn() => view('welcome'));

// ── Page banni ─────────────────────────────────────────────
Route::get('/banned', fn() => view('banned'))->middleware('auth')->name('banned');

// ── Routes authentifiées ───────────────────────────────────
Route::middleware(['auth', 'verified', 'isBanned', 'redirect.after.invitation.login'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Wallet
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('wallet.deposit');

    // Colocations - CRUD
    Route::resource('colocations', ColocationController::class);

    // Colocations - Actions supplémentaires
    Route::post(
        '/colocations/{colocation}/cancel',
        [ColocationController::class, 'cancel']
    )->name('colocations.cancel');
    Route::post(
        '/colocations/{colocation}/leave',
        [ColocationController::class, 'leave']
    )->name('colocations.leave');
    Route::delete(
        '/colocations/{colocation}/members/{user}',
        [ColocationController::class, 'removeMember']
    )->name('colocations.removeMember');

    // Dépenses
    Route::post(
        '/colocations/{colocation}/expenses',
        [ExpenseController::class, 'store']
    )->name('colocations.expenses.store');
    Route::delete(
        '/expenses/{expense}',
        [ExpenseController::class, 'destroy']
    )->name('expenses.destroy');

    // Invitations
    Route::post(
        '/colocations/{colocation}/invite',
        [InvitationController::class, 'send']
    )->name('invitations.send');

    // Paiements
    Route::post(
        '/payments',
        [PaymentController::class, 'store']
    )->name('payments.store');
});

// ── Routes Invitations (publiques pour redirection) ─────────────────────────────
Route::get(
    '/invitations/{token}',
    [InvitationController::class, 'show']
)->name('invitations.show');

// ── Routes Admin ───────────────────────────────────────────
Route::middleware(['auth', 'verified', 'isBanned', 'isAdmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/users/{user}/ban', [AdminController::class, 'ban'])->name('ban');
        Route::post('/users/{user}/unban', [AdminController::class, 'unban'])->name('unban');
    });


Route::middleware('auth')->group(function () {
    Route::get(
        '/invitations/{token}/accept',
        [InvitationController::class, 'accept']
    )->name('invitations.accept');
    Route::get(
        '/invitations/{token}/refuse',
        [InvitationController::class, 'refuse']
    )->name('invitations.refuse');
});
// ── Auth (Breeze) ──────────────────────────────────────────
require __DIR__ . '/auth.php';
