<?php

namespace App\Http\Controllers;

use App\Models\Anime;
use App\Models\Genero;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Consume un servicio web EXTERNO y público: AniList (https://anilist.co).
 *
 * AniList expone una API GraphQL: todas las consultas se envían por HTTP con
 * el método POST a una sola URL, con cabeceras Content-Type y Accept en JSON,
 * y en el cuerpo van la consulta ("query") y sus variables.
 *
 * Permite buscar animes en internet e importarlos a nuestra base de datos.
 */
class AnilistController extends Controller
{
    /** Campos que pedimos de cada anime (fragmento GraphQL reutilizable). */
    private const CAMPOS = '
        id
        title { romaji english }
        seasonYear
        startDate { year }
        episodes
        status
        averageScore
        genres
        description(asHtml: false)
        coverImage { large medium }
        studios(isMain: true) { nodes { name } }
    ';

    private const MAPA_GENEROS = [
        'Action' => 'Acción', 'Adventure' => 'Aventura', 'Sci-Fi' => 'Ciencia ficción',
        'Comedy' => 'Comedia', 'Drama' => 'Drama', 'Fantasy' => 'Fantasía', 'Horror' => 'Terror',
        'Romance' => 'Romance', 'Thriller' => 'Suspenso', 'Mystery' => 'Suspenso',
        'Psychological' => 'Suspenso', 'Slice of Life' => 'Slice of life',
    ];

    private const MAPA_ESTADOS = [
        'RELEASING' => 'En emisión', 'FINISHED' => 'Finalizado', 'NOT_YET_RELEASED' => 'Próximamente',
        'CANCELLED' => 'Finalizado', 'HIATUS' => 'En emisión',
    ];

    /** GET /anilist?q=texto → busca animes en AniList */
    public function index(Request $request)
    {
        $resultados = [];
        $error = null;

        if ($request->filled('q')) {
            $query = 'query ($buscar: String) {
                Page(perPage: 12) {
                    media(search: $buscar, type: ANIME, isAdult: false, sort: SEARCH_MATCH) {'.self::CAMPOS.'}
                }
            }';

            [$respuesta, $error] = $this->consultar($query, ['buscar' => $request->q]);

            if ($respuesta) {
                $resultados = collect($respuesta->json('data.Page.media', []))
                    ->map(fn ($a) => $this->normalizar($a))
                    ->all();
            }
        }

        return view('anilist.index', [
            'resultados' => $resultados,
            'q' => $request->q,
            'error' => $error,
        ]);
    }

    /** POST /anilist/importar → trae un anime por su id y lo guarda en nuestra BD */
    public function importar(Request $request)
    {
        $request->validate(['anilist_id' => ['required', 'integer']]);

        $query = 'query ($id: Int) { Media(id: $id, type: ANIME) {'.self::CAMPOS.'} }';

        [$respuesta, $error] = $this->consultar($query, ['id' => (int) $request->anilist_id]);

        if (! $respuesta) {
            return back()->withErrors(['anilist' => $error]);
        }

        $a = $this->normalizar($respuesta->json('data.Media'));

        if (Anime::where('titulo', $a['titulo'])->exists()) {
            return back()->withErrors(['anilist' => "«{$a['titulo']}» ya existe en tu catálogo."]);
        }

        $genero = collect($a['generos'])->map(fn ($g) => self::MAPA_GENEROS[$g] ?? null)->filter()->first() ?? 'Animación';

        Anime::create([
            'titulo' => $a['titulo'],
            'estudio' => $a['estudio'],
            'genero_id' => Genero::firstOrCreate(['nombre' => $genero])->id,
            'anio' => $a['anio'] ?? (int) date('Y'),
            'episodios' => max(1, $a['episodios'] ?? 1),
            'estado' => $a['estado'],
            'calificacion' => $a['calificacion'] ?? 0,
            'sinopsis' => $a['sinopsis'],
            'imagen' => $a['imagen'],                 // URL externa de AniList
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('animes.index')->with('ok', "«{$a['titulo']}» se importó desde AniList.");
    }

    // ----------------------------------------------------------------------

    /** Cliente HTTP con la URL base y las cabeceras que exige AniList. */
    private function cliente(): PendingRequest
    {
        return Http::acceptJson()                         // Accept: application/json
            ->asJson()                                     // Content-Type: application/json
            ->withHeaders(['User-Agent' => config('app.name').'/1.0'])
            ->timeout(20);
    }

    /**
     * Envía la consulta GraphQL con POST. Devuelve [respuesta, null] si salió bien
     * o [null, mensaje de error] si falló.
     *
     * @return array{0: ?Response, 1: ?string}
     */
    private function consultar(string $query, array $variables): array
    {
        try {
            $r = $this->cliente()->post(config('services.anilist.url'), [
                'query' => $query,
                'variables' => $variables,
            ]);
        } catch (ConnectionException $e) {
            return [null, 'No fue posible conectarse con AniList: '.$e->getMessage()];
        }

        if ($r->status() === 429) {
            return [null, 'AniList recibió demasiadas consultas. Espera un minuto e inténtalo de nuevo.'];
        }
        if ($r->failed() || $r->json('errors')) {
            $detalle = $r->json('errors.0.message') ?? 'estado '.$r->status();

            return [null, 'AniList respondió con un error: '.$detalle];
        }

        return [$r, null];
    }

    /** Convierte la respuesta de AniList a un arreglo sencillo para la vista y la BD. */
    private function normalizar(array $a): array
    {
        $sinopsis = trim(html_entity_decode(strip_tags(preg_replace('/<br\s*\/?>/i', "\n", (string) ($a['description'] ?? '')))));

        return [
            'id' => $a['id'],
            'titulo' => $a['title']['english'] ?? $a['title']['romaji'],
            'anio' => $a['seasonYear'] ?? $a['startDate']['year'] ?? null,
            'episodios' => $a['episodes'] ?? null,
            'estado' => self::MAPA_ESTADOS[$a['status'] ?? ''] ?? 'Finalizado',
            'calificacion' => isset($a['averageScore']) ? round($a['averageScore'] / 10, 1) : null,
            'generos' => $a['genres'] ?? [],
            'estudio' => collect($a['studios']['nodes'] ?? [])->pluck('name')->join(' / ') ?: null,
            'sinopsis' => $sinopsis !== '' ? mb_strimwidth($sinopsis, 0, 1990, '…') : null,
            'imagen' => $a['coverImage']['large'] ?? $a['coverImage']['medium'] ?? null,
        ];
    }
}
