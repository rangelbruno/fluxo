<?php

use App\Http\Controllers\Api\ComprovantePagamentoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Health check para n8n
Route::get('/health', [ComprovantePagamentoController::class, 'health'])
    ->name('api.health');

// Rotas de comprovantes (para integração com n8n)
Route::prefix('v1')->group(function () {
    Route::apiResource('comprovantes', ComprovantePagamentoController::class)
        ->parameters(['comprovantes' => 'comprovante']);
});
