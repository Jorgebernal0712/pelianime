<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeliculaRequest;
use App\Models\Genero;
use App\Models\Pelicula;
use Illuminate\Support\Facades\Storage;

class PeliculaController extends Controller
{
    public function index()
    {
        $peliculas = Pelicula::with('genero')->latest()->get();

        return view('peliculas.index', compact('peliculas'));
    }

    public function create()
    {
        return view('peliculas.create', ['pelicula' => new Pelicula, 'generos' => Genero::orderBy('nombre')->get()]);
    }

    public function store(PeliculaRequest $request)
    {
        $datos = $request->validated();
        $datos['user_id'] = $request->user()->id;

        if ($request->hasFile('poster')) {
            $datos['poster'] = $request->file('poster')->store('peliculas', 'public');
        }

        Pelicula::create($datos);

        return redirect()->route('peliculas.index')->with('ok', 'Película creada correctamente.');
    }

    public function show(Pelicula $pelicula)
    {
        $pelicula->load('genero', 'user');

        return view('peliculas.show', compact('pelicula'));
    }

    public function edit(Pelicula $pelicula)
    {
        return view('peliculas.edit', ['pelicula' => $pelicula, 'generos' => Genero::orderBy('nombre')->get()]);
    }

    public function update(PeliculaRequest $request, Pelicula $pelicula)
    {
        $datos = $request->validated();

        if ($request->hasFile('poster')) {
            $this->borrarImagen($pelicula->poster);
            $datos['poster'] = $request->file('poster')->store('peliculas', 'public');
        } else {
            unset($datos['poster']);
        }

        $pelicula->update($datos);

        return redirect()->route('peliculas.index')->with('ok', 'Película actualizada correctamente.');
    }

    public function destroy(Pelicula $pelicula)
    {
        $this->borrarImagen($pelicula->poster);
        $pelicula->delete();

        return redirect()->route('peliculas.index')->with('ok', 'Película eliminada.');
    }

    private function borrarImagen(?string $ruta): void
    {
        if ($ruta && ! str_starts_with($ruta, 'http')) {
            Storage::disk('public')->delete($ruta);
        }
    }
}
