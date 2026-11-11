<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PeliculaRequest;
use App\Models\Pelicula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PeliculaApiController extends Controller
{
    /** GET /api/peliculas  (filtros opcionales ?buscar=  ?genero_id=) */
    public function index(Request $request)
    {
        $query = Pelicula::with('genero:id,nombre')->latest();

        if ($request->filled('buscar')) {
            $query->where('titulo', 'like', '%'.$request->buscar.'%');
        }
        if ($request->filled('genero_id')) {
            $query->where('genero_id', $request->genero_id);
        }

        return response()->json($query->get());
    }

    /** POST /api/peliculas */
    public function store(PeliculaRequest $request)
    {
        $datos = $request->validated();
        $datos['user_id'] = $request->user()->id;

        if ($request->hasFile('poster')) {
            $datos['poster'] = $request->file('poster')->store('peliculas', 'public');
        }

        $pelicula = Pelicula::create($datos)->load('genero:id,nombre');

        return response()->json(['message' => 'Película creada', 'data' => $pelicula], 201);
    }

    /** GET /api/peliculas/{id} */
    public function show(Pelicula $pelicula)
    {
        return response()->json($pelicula->load('genero:id,nombre'));
    }

    /** PUT /api/peliculas/{id} */
    public function update(PeliculaRequest $request, Pelicula $pelicula)
    {
        $datos = $request->validated();

        if ($request->hasFile('poster')) {
            if ($pelicula->poster && ! str_starts_with($pelicula->poster, 'http')) {
                Storage::disk('public')->delete($pelicula->poster);
            }
            $datos['poster'] = $request->file('poster')->store('peliculas', 'public');
        }

        $pelicula->update($datos);

        return response()->json(['message' => 'Película actualizada', 'data' => $pelicula->fresh('genero:id,nombre')]);
    }

    /** DELETE /api/peliculas/{id} */
    public function destroy(Pelicula $pelicula)
    {
        if ($pelicula->poster && ! str_starts_with($pelicula->poster, 'http')) {
            Storage::disk('public')->delete($pelicula->poster);
        }
        $pelicula->delete();

        return response()->json(['message' => 'Película eliminada']);
    }
}
