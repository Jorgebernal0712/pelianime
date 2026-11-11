<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Genero;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Consume un servicio web EXTERNO y público: Jikan (API no oficial de MyAnimeList).
 * Permite buscar animes en internet e importarlos a nuestra base de datos.
 */
class JikanController extends Controller
{
    private const MAPA_GENEROS = [
        'Action' => 'Acción', 'Adventure' => 'Aventura', 'Sci-Fi' => 'Ciencia ficción',
        'Comedy' => 'Comedia', 'Drama' => 'Drama', 'Fantasy' => 'Fantasía', 'Horror' => 'Terror',
        'Romance' => 'Romance', 'Suspense' => 'Suspenso', 'Mystery' => 'Suspenso',
        'Slice of Life' => 'Slice of life', 'Shounen' => 'Shōnen',
    ];

    private const MAPA_ESTADOS = [
        'Currently Airing' => 'En emisión', 'Finished Airing' => 'Finalizado', 'Not yet aired' => 'Próximamente',
    ];

    public function index(Request $request)
    {
        $resultados = [];
        $error = null;

        if ($request->filled('q')) {
            try {
                $r = $this->cliente()->get('/anime', ['q' => $request->q, 'limit' => 12, 'sfw' => 'true']);
                $r->successful()
                    ? $resultados = $r->json('data', [])
                    : $error = 'Jikan respondió con estado '.$r->status().'. Intenta de nuevo en unos segundos.';
            } catch (ConnectionException|RequestException $e) {
                $error = 'No fue posible conectarse con Jikan. Revisa tu conexión a internet.';
            }
        }

        return view('jikan.index', ['resultados' => $resultados, 'q' => $request->q, 'error' => $error]);
    }

    public function importar(Request $request)
    {
        $request->validate(['mal_id' => ['required', 'integer']]);

        try {
            $r = $this->cliente()->get('/anime/'.$request->mal_id);
        } catch (ConnectionException|RequestException $e) {
            return back()->withErrors(['jikan' => 'No fue posible conectarse con Jikan.']);
        }

        if ($r->failed()) {
            return back()->withErrors(['jikan' => 'Jikan respondió con estado '.$r->status()]);
        }

        $a = $r->json('data');

        if (Anime::where('titulo', $a['title'])->exists()) {
            return back()->withErrors(['jikan' => "«{$a['title']}» ya existe en tu catálogo."]);
        }

        $nombresGenero = collect(array_merge($a['genres'] ?? [], $a['demographics'] ?? []))->pluck('name');
        $genero = $nombresGenero->map(fn ($n) => self::MAPA_GENEROS[$n] ?? null)->filter()->first() ?? 'Animación';

        Anime::create([
            'titulo' => $a['title'],
            'estudio' => collect($a['studios'] ?? [])->pluck('name')->join(' / ') ?: null,
            'genero_id' => Genero::firstOrCreate(['nombre' => $genero])->id,
            'anio' => $a['year'] ?? (isset($a['aired']['prop']['from']['year']) ? $a['aired']['prop']['from']['year'] : date('Y')),
            'episodios' => $a['episodes'] ?? 1,
            'estado' => self::MAPA_ESTADOS[$a['status']] ?? 'Finalizado',
            'calificacion' => $a['score'] ?? 0,
            'sinopsis' => isset($a['synopsis']) ? mb_strimwidth($a['synopsis'], 0, 1990, '…') : null,
            'imagen' => $a['images']['jpg']['large_image_url'] ?? $a['images']['jpg']['image_url'] ?? null,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('animes.index')->with('ok', "«{$a['title']}» se importó desde Jikan.");
    }

    private function cliente()
    {
        return Http::baseUrl(config('services.jikan.url'))
            ->acceptJson()
            ->withHeaders(['User-Agent' => config('app.name').'/1.0'])
            ->timeout(15);
    }
}
