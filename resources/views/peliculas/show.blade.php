@extends('layouts.app')

@section('title', $pelicula->titulo)

@section('content')
<div class="card-pa p-4">
    <div class="row g-4">
        <div class="col-md-4 col-lg-3">
            @include('partials.poster', ['titulo' => $pelicula->titulo, 'imagen' => $pelicula->poster_url, 'sub' => $pelicula->anio, 'calificacion' => $pelicula->calificacion, 'icono' => 'bi-film'])
        </div>
        <div class="col-md-8 col-lg-9">
            <span class="badge badge-genre mb-2">{{ $pelicula->genero->nombre }}</span>
            <h2 class="fw-bold">{{ $pelicula->titulo }}</h2>
            <p class="text-muted">{{ $pelicula->director ?? 'Director desconocido' }} · {{ $pelicula->anio }} @if($pelicula->duracion_min) · {{ $pelicula->duracion_min }} min @endif</p>
            <p class="rating fs-5"><i class="bi bi-star-fill"></i> {{ number_format($pelicula->calificacion, 1) }} / 10</p>
            <p>{{ $pelicula->sinopsis ?? 'Sin sinopsis.' }}</p>
            <p class="small text-muted">Registrada por {{ $pelicula->user->name ?? '—' }} el {{ $pelicula->created_at->format('d/m/Y H:i') }}</p>
            <div class="d-flex gap-2">
                <a href="{{ route('peliculas.edit', $pelicula) }}" class="btn btn-pa btn-sm"><i class="bi bi-pencil"></i> Editar</a>
                <a href="{{ route('peliculas.index') }}" class="btn btn-ghost btn-sm">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection
