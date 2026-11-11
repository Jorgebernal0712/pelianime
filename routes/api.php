<?php

use App\Http\Controllers\Api\AnimeApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\GeneroApiController;
use App\Http\Controllers\Api\PeliculaApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API REST  (prefijo automático: /api)
|--------------------------------------------------------------------------
| 1) POST /api/login  con email y password  → devuelve access_token
| 2) Enviar en cada petición:  Authorization: Bearer {access_token}
|                              Accept: application/json
*/

// Públicas
Route::post('/login', [AuthApiController::class, 'login'])->middleware('throttle:10,1');
Route::get('/generos', [GeneroApiController::class, 'index']);

// Protegidas con token (Laravel Sanctum)
Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::get('/me', [AuthApiController::class, 'me'])->name('me');
    Route::post('/logout', [AuthApiController::class, 'logout'])->name('logout');

    Route::apiResource('peliculas', PeliculaApiController::class);
    Route::apiResource('animes', AnimeApiController::class);
});
