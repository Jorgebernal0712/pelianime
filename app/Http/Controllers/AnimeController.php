<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimeRequest;
use App\Models\Genero;
use App\Models\Anime;
use Illuminate\Support\Facades\Storage;

class AnimeController extends Controller
{
    public function index()
    {
        $animes = Anime::with('genero')->latest()->get();

        return view('animes.index', compact('animes'));
    }

    public function create()
    {
        return view('animes.create', ['anime' => new Anime, 'generos' => Genero::orderBy('nombre')->get()]);
    }

    public function store(AnimeRequest $request)
    {
        $datos = $request->validated();
        $datos['user_id'] = $request->user()->id;

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('animes', 'public');
        }

        Anime::create($datos);

        return redirect()->route('animes.index')->with('ok', 'Anime creado correctamente.');
    }

    public function show(Anime $anime)
    {
        $anime->load('genero', 'user');

        return view('animes.show', compact('anime'));
    }

    public function edit(Anime $anime)
    {
        return view('animes.edit', ['anime' => $anime, 'generos' => Genero::orderBy('nombre')->get()]);
    }

    public function update(AnimeRequest $request, Anime $anime)
    {
        $datos = $request->validated();

        if ($request->hasFile('imagen')) {
            $this->borrarImagen($anime->imagen);
            $datos['imagen'] = $request->file('imagen')->store('animes', 'public');
        } else {
            unset($datos['imagen']);
        }

        $anime->update($datos);

        return redirect()->route('animes.index')->with('ok', 'Anime actualizado correctamente.');
    }

    public function destroy(Anime $anime)
    {
        $this->borrarImagen($anime->imagen);
        $anime->delete();

        return redirect()->route('animes.index')->with('ok', 'Anime eliminado.');
    }

    private function borrarImagen(?string $ruta): void
    {
        if ($ruta && ! str_starts_with($ruta, 'http')) {
            Storage::disk('public')->delete($ruta);
        }
    }
}
