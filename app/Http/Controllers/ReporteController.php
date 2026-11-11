<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Pelicula;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /** Descarga un PDF con el listado de películas. */
    public function peliculas()
    {
        $registros = Pelicula::with('genero')->orderByDesc('calificacion')->get();

        $pdf = Pdf::loadView('reportes.peliculas', [
            'registros' => $registros,
            'promedio' => round((float) $registros->avg('calificacion'), 1),
            'fecha' => now(),
            'usuario' => auth()->user(),
        ])->setPaper('letter', 'landscape');

        return $pdf->download('reporte-peliculas-'.now()->format('Ymd-His').'.pdf');
    }

    /** Descarga un PDF con el listado de animes. */
    public function animes()
    {
        $registros = Anime::with('genero')->orderByDesc('calificacion')->get();

        $pdf = Pdf::loadView('reportes.animes', [
            'registros' => $registros,
            'promedio' => round((float) $registros->avg('calificacion'), 1),
            'fecha' => now(),
            'usuario' => auth()->user(),
        ])->setPaper('letter', 'landscape');

        return $pdf->download('reporte-animes-'.now()->format('Ymd-His').'.pdf');
    }
}
