<?php

use App\Http\Controllers\AnimeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ClienteApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JikanController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Models\Anime;
use App\Models\Pelicula;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome', [
        'destacadasPeliculas' => Pelicula::with('genero')->orderByDesc('calificacion')->take(4)->get(),
        'destacadosAnimes' => Anime::with('genero')->orderByDesc('calificacion')->take(4)->get(),
        'totalPeliculas' => Pelicula::count(),
        'totalAnimes' => Anime::count(),
    ]);
})->name('home');

/*
|--------------------------------------------------------------------------
| Solo visitantes (si ya inició sesión lo redirige al dashboard)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');

    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.post');

    // Recuperación de contraseña
    Route::get('/olvide-contrasena', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/olvide-contrasena', [PasswordResetController::class, 'sendLink'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/restablecer-contrasena/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/restablecer-contrasena', [PasswordResetController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren sesión iniciada)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil del usuario activo
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // CRUD completos
    Route::resource('peliculas', PeliculaController::class);
    Route::resource('animes', AnimeController::class);

    // Reportes PDF
    Route::get('/reportes/peliculas.pdf', [ReporteController::class, 'peliculas'])->name('reportes.peliculas');
    Route::get('/reportes/animes.pdf', [ReporteController::class, 'animes'])->name('reportes.animes');

    // Consumo de servicios web
    Route::prefix('cliente-api')->name('cliente-api.')->group(function () {
        Route::get('/', [ClienteApiController::class, 'index'])->name('index');
        Route::post('/peliculas', [ClienteApiController::class, 'store'])->name('store');
        Route::put('/peliculas/{id}', [ClienteApiController::class, 'update'])->name('update');
        Route::delete('/peliculas/{id}', [ClienteApiController::class, 'destroy'])->name('destroy');
        Route::post('/token', [ClienteApiController::class, 'regenerarToken'])->name('token');
    });

    Route::get('/jikan', [JikanController::class, 'index'])->name('jikan.index');
    Route::post('/jikan/importar', [JikanController::class, 'importar'])->name('jikan.importar');
});
