@extends('layouts.app')

@section('title', 'Importar animes desde AniList')

@section('content')
<div class="card-pa p-4 mb-4">
    <p class="small text-muted mb-2">
        Esta sección consume el servicio web público <b>AniList</b>, una API <b>GraphQL</b>.
        Cada búsqueda se envía por HTTP con el método <span class="method method-POST">POST</span> a
        <code>{{ config('services.anilist.url') }}</code>, con las cabeceras
        <code>Content-Type: application/json</code> y <code>Accept: application/json</code>,
        y en el cuerpo la consulta y sus variables. Luego puedes importar el resultado a tu catálogo.
    </p>
    <form method="GET" action="{{ route('anilist.index') }}" class="d-flex gap-2">
        <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Ej: Naruto, Bleach, Chainsaw Man…" required minlength="2">
        <button class="btn btn-pa px-4"><i class="bi bi-search"></i> Buscar</button>
    </form>
</div>

@if ($error)
    <div class="alert alert-warning">{{ $error }}</div>
@endif
@error('anilist')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror

<div class="row g-3">
    @foreach ($resultados as $r)
        <div class="col-6 col-md-4 col-xl-2">
            <div class="poster-card mb-2">
                @if ($r['imagen'])
                    <img src="{{ $r['imagen'] }}" alt="{{ $r['titulo'] }}" loading="lazy">
                @else
                    <div class="placeholder-art"><i class="bi bi-stars"></i></div>
                @endif
                <div class="info">
                    <div class="fw-semibold small lh-sm">{{ $r['titulo'] }}</div>
                    <div class="d-flex justify-content-between small mt-1">
                        <span class="text-muted">{{ $r['anio'] ?? '—' }} · {{ $r['episodios'] ?? '?' }} eps</span>
                        <span class="rating"><i class="bi bi-star-fill"></i> {{ $r['calificacion'] ?? '—' }}</span>
                    </div>
                </div>
            </div>
            <form action="{{ route('anilist.importar') }}" method="POST">
                @csrf
                <input type="hidden" name="anilist_id" value="{{ $r['id'] }}">
                <button class="btn btn-ghost btn-sm w-100"><i class="bi bi-download"></i> Importar</button>
            </form>
        </div>
    @endforeach
</div>

@if ($q && ! $error && empty($resultados))
    <p class="text-muted">No se encontraron resultados para «{{ $q }}».</p>
@endif
@endsection
