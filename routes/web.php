<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ComprovantesController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [LoginController::class, 'showLoginForm']);
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Comprovantes
    Route::prefix('comprovantes')->name('comprovantes.')->group(function (): void {
        Route::get('/', [ComprovantesController::class, 'index'])->name('index');
        Route::post('/processar', [ComprovantesController::class, 'processar'])->name('processar');
        Route::get('/{comprovante}', [ComprovantesController::class, 'show'])->name('show');
        Route::delete('/{comprovante}', [ComprovantesController::class, 'destroy'])->name('destroy');
    });

    // Configuracoes
    Route::prefix('configuracoes')->name('configuracoes.')->group(function (): void {
        Route::get('/n8n', [ConfiguracaoController::class, 'n8n'])->name('n8n');
    });
});
