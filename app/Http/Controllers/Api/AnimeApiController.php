<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnimeRequest;
use App\Models\Anime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnimeApiController extends Controller
{
    /** GET /api/animes  (filtros opcionales ?buscar=  ?genero_id=) */
    public function index(Request $request)
    {
        $query = Anime::with('genero:id,nombre')->latest();

        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%'.$request->buscar.'%');
        }
        if ($request->filled('genero_id')) {
            $query->where('genero_id', $request->genero_id);
        }

        return response()->json($query->get());
    }

    /** POST /api/animes */
    public function store(AnimeRequest $request)
    {
        $datos = $request->validated();
        $datos['user_id'] = $request->user()->id;

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('animes', 'public');
        }

        $anime = Anime::create($datos)->load('genero:id,nombre');

        return response()->json(['message' => 'Anime creado', 'data' => $anime], 201);
    }

    /** GET /api/animes/{id} */
    public function show(Anime $anime)
    {
        return response()->json($anime->load('genero:id,nombre'));
    }

    /** PUT /api/animes/{id} */
    public function update(AnimeRequest $request, Anime $anime)
    {
        $datos = $request->validated();

        if ($request->hasFile('imagen')) {
            if ($anime->imagen && ! str_starts_with($anime->imagen, 'http')) {
                Storage::disk('public')->delete($anime->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('animes', 'public');
        }

        $anime->update($datos);

        return response()->json(['message' => 'Anime actualizado', 'data' => $anime->fresh('genero:id,nombre')]);
    }

    /** DELETE /api/animes/{id} */
    public function destroy(Anime $anime)
    {
        if ($anime->imagen && ! str_starts_with($anime->imagen, 'http')) {
            Storage::disk('public')->delete($anime->imagen);
        }
        $anime->delete();

        return response()->json(['message' => 'Anime eliminado']);
    }
}
