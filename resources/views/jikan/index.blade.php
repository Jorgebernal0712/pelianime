@extends('layouts.app')

@section('title', 'Importar animes desde Jikan')

@section('content')
<div class="card-pa p-4 mb-4">
    <p class="small text-muted">
        Esta sección consume el servicio web público <b>Jikan</b> (<code>{{ config('services.jikan.url') }}</code>), una API REST
        de MyAnimeList. Buscamos con <span class="method method-GET">GET</span> y puedes importar el resultado a tu catálogo.
    </p>
    <form method="GET" action="{{ route('jikan.index') }}" class="d-flex gap-2">
        <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Ej: Naruto, Bleach, Chainsaw Man…" required minlength="2">
        <button class="btn btn-pa px-4"><i class="bi bi-search"></i> Buscar</button>
    </form>
</div>

@if ($error)
    <div class="alert alert-warning">{{ $error }}</div>
@endif
@error('jikan')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror

<div class="row g-3">
    @foreach ($resultados as $r)
        <div class="col-6 col-md-4 col-xl-2">
            <div class="poster-card mb-2">
                <img src="{{ $r['images']['jpg']['image_url'] ?? '' }}" alt="{{ $r['title'] }}" loading="lazy">
                <div class="info">
                    <div class="fw-semibold small lh-sm">{{ $r['title'] }}</div>
                    <div class="d-flex justify-content-between small mt-1">
                        <span class="text-muted">{{ $r['year'] ?? '—' }} · {{ $r['episodes'] ?? '?' }} eps</span>
                        <span class="rating"><i class="bi bi-star-fill"></i> {{ $r['score'] ?? '—' }}</span>
                    </div>
                </div>
            </div>
            <form action="{{ route('jikan.importar') }}" method="POST">
                @csrf
                <input type="hidden" name="mal_id" value="{{ $r['mal_id'] }}">
                <button class="btn btn-ghost btn-sm w-100"><i class="bi bi-download"></i> Importar</button>
            </form>
        </div>
    @endforeach
</div>

@if ($q && ! $error && empty($resultados))
    <p class="text-muted">No se encontraron resultados para «{{ $q }}».</p>
@endif
@endsection
