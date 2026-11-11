<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Cliente que CONSUME nuestra propia API REST (como lo haría una app externa)
 * usando el cliente HTTP de Laravel con:
 *   - Token Bearer de Sanctum (cabecera Authorization)
 *   - Cabeceras Accept: application/json y X-Cliente
 *   - Métodos GET, POST, PUT y DELETE
 */
class ClienteApiController extends Controller
{
    public function index(Request $request)
    {
        $token = $this->token($request);
        $peliculas = [];
        $error = null;

        try {
            $respuesta = $this->cliente($token)->get('/peliculas');
            // Si venimos de un POST/PUT/DELETE se muestra esa petición; si no, este GET
            if (! session()->has('ultima_peticion')) {
                $this->registrar($request, 'GET', '/peliculas', $respuesta, $token, false);
            }
            $peliculas = $respuesta->successful() ? $respuesta->json() : [];
            if ($respuesta->failed()) {
                $error = 'La API respondió con estado '.$respuesta->status();
            }
        } catch (ConnectionException $e) {
            $error = 'No se pudo conectar con la API ('.config('services.pelianime_api.url').'). '.
                'En local inicia el servidor con "php artisan serve --no-reload" y PHP_CLI_SERVER_WORKERS=4 en tu .env.';
        }

        return view('cliente-api.index', [
            'peliculas' => $peliculas,
            'generos' => Genero::orderBy('nombre')->get(),
            'tokenMascara' => substr($token, 0, 6).str_repeat('•', 18).substr($token, -4),
            'baseUrl' => config('services.pelianime_api.url'),
            'ultima' => session('ultima_peticion'),
            'error' => $error,
        ]);
    }

    /** POST → crea una película a través de la API */
    public function store(Request $request)
    {
        $payload = $request->only('titulo', 'director', 'genero_id', 'anio', 'duracion_min', 'calificacion', 'sinopsis');

        return $this->ejecutar($request, 'POST', '/peliculas', $payload);
    }

    /** PUT → actualiza una película a través de la API */
    public function update(Request $request, int $id)
    {
        $payload = array_filter(
            $request->only('titulo', 'calificacion', 'anio'),
            fn ($v) => $v !== null && $v !== ''
        );

        return $this->ejecutar($request, 'PUT', "/peliculas/{$id}", $payload);
    }

    /** DELETE → elimina una película a través de la API */
    public function destroy(Request $request, int $id)
    {
        return $this->ejecutar($request, 'DELETE', "/peliculas/{$id}");
    }

    /** Revoca el token actual y genera uno nuevo. */
    public function regenerarToken(Request $request)
    {
        $request->user()->tokens()->where('name', 'cliente-web')->delete();
        $request->session()->forget('api_token');
        $this->token($request);

        return back()->with('ok', 'Se revocó el token anterior y se generó uno nuevo.');
    }

    // ----------------------------------------------------------------------

    private function ejecutar(Request $request, string $metodo, string $ruta, array $payload = [])
    {
        $token = $this->token($request);

        try {
            $respuesta = match ($metodo) {
                'POST' => $this->cliente($token)->post($ruta, $payload),
                'PUT' => $this->cliente($token)->put($ruta, $payload),
                'DELETE' => $this->cliente($token)->delete($ruta),
            };
        } catch (ConnectionException $e) {
            return back()->withErrors(['api' => 'No se pudo conectar con la API: '.$e->getMessage()]);
        }

        $this->registrar($request, $metodo, $ruta, $respuesta, $token, true, $payload);

        if ($respuesta->failed()) {
            $mensaje = $respuesta->json('message') ?? 'Error '.$respuesta->status();

            return back()->withErrors(['api' => "La API respondió {$respuesta->status()}: {$mensaje}"]);
        }

        return back()->with('ok', "{$metodo} {$ruta} → {$respuesta->status()} ".($respuesta->json('message') ?? 'OK'));
    }

    /** Cliente HTTP configurado con URL base, token y cabeceras. */
    private function cliente(string $token): PendingRequest
    {
        return Http::baseUrl(config('services.pelianime_api.url'))
            ->withToken($token)                          // Authorization: Bearer xxx
            ->acceptJson()                               // Accept: application/json
            ->withHeaders(['X-Cliente' => 'PeliAnime-Web'])
            ->timeout(15);
    }

    /** Obtiene (o crea) el token personal del usuario y lo guarda en sesión. */
    private function token(Request $request): string
    {
        if (! $request->session()->has('api_token')) {
            $token = $request->user()->createToken('cliente-web')->plainTextToken;
            $request->session()->put('api_token', $token);
        }

        return $request->session()->get('api_token');
    }

    /** Guarda la última petición para mostrarla en pantalla (método, cabeceras, respuesta). */
    private function registrar(Request $request, string $metodo, string $ruta, Response $r, string $token, bool $flash, array $payload = []): void
    {
        $info = [
            'metodo' => $metodo,
            'url' => rtrim(config('services.pelianime_api.url'), '/').$ruta,
            'cabeceras' => [
                'Authorization' => 'Bearer '.substr($token, 0, 6).'…',
                'Accept' => 'application/json',
                'X-Cliente' => 'PeliAnime-Web',
            ],
            'cuerpo' => $payload,
            'estado' => $r->status(),
            'respuesta' => mb_strimwidth(json_encode($r->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: $r->body(), 0, 3000, '…'),
        ];

        $flash ? $request->session()->flash('ultima_peticion', $info) : session()->now('ultima_peticion', $info);
    }
}
