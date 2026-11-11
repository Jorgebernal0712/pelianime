<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Genero;
use App\Models\Pelicula;

class DashboardController extends Controller
{
    public function index()
    {
        // Datos para la gráfica: cantidad de películas y animes por género
        $generos = Genero::withCount(['peliculas', 'animes'])->orderBy('nombre')->get();

        $grafica = [
            'labels' => $generos->pluck('nombre'),
            'peliculas' => $generos->pluck('peliculas_count'),
            'animes' => $generos->pluck('animes_count'),
        ];

        // Segunda gráfica: animes por estado
        $estados = Anime::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->pluck('total', 'estado');

        return view('dashboard.index', [
            'totalPeliculas' => Pelicula::count(),
            'totalAnimes' => Anime::count(),
            'promPeliculas' => round((float) Pelicula::avg('calificacion'), 1),
            'promAnimes' => round((float) Anime::avg('calificacion'), 1),
            'ultimasPeliculas' => Pelicula::with('genero')->latest()->take(5)->get(),
            'ultimosAnimes' => Anime::with('genero')->latest()->take(5)->get(),
            'grafica' => $grafica,
            'estados' => $estados,
        ]);
    }
}
