@extends('layouts.app')

@section('title', $anime->titulo)

@section('content')
<div class="card-pa p-4">
    <div class="row g-4">
        <div class="col-md-4 col-lg-3">
            @include('partials.poster', ['titulo' => $anime->titulo, 'imagen' => $anime->imagen_url, 'sub' => $anime->anio, 'calificacion' => $anime->calificacion, 'icono' => 'bi-stars'])
        </div>
        <div class="col-md-8 col-lg-9">
            <span class="badge badge-genre mb-2">{{ $anime->genero->nombre }}</span>
            <span class="badge text-bg-secondary mb-2">{{ $anime->estado }}</span>
            <h2 class="fw-bold">{{ $anime->titulo }}</h2>
            <p class="text-muted">{{ $anime->estudio ?? 'Estudio desconocido' }} · {{ $anime->anio }} · {{ $anime->episodios }} episodios</p>
            <p class="rating fs-5"><i class="bi bi-star-fill"></i> {{ number_format($anime->calificacion, 1) }} / 10</p>
            <p>{{ $anime->sinopsis ?? 'Sin sinopsis.' }}</p>
            <p class="small text-muted">Registrado por {{ $anime->user->name ?? '—' }} el {{ $anime->created_at->format('d/m/Y H:i') }}</p>
            <div class="d-flex gap-2">
                <a href="{{ route('animes.edit', $anime) }}" class="btn btn-pa btn-sm"><i class="bi bi-pencil"></i> Editar</a>
                <a href="{{ route('animes.index') }}" class="btn btn-ghost btn-sm">Volver</a>
            </div>
        </div>
    </div>
</div>
@endsection
