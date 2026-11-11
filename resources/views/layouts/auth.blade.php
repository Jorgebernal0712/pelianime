{{-- ============================================================
     MAESTRA DE AUTENTICACIÓN: login, registro, recuperar contraseña.
     Hereda de layouts.base.
     ============================================================ --}}
@extends('layouts.base')

@section('body')
<div class="auth-wrap">
    <div class="auth-art">
        <a href="{{ route('home') }}" class="brand fs-4 fw-bold text-white text-decoration-none"><span class="text-gradient">●</span> PeliAnime</a>
        <div class="film-strip"></div>
        <div style="position:relative; max-width: 460px">
            <h2 class="display-6 fw-bold mb-3">Tu catálogo de <span class="text-gradient">películas y animes</span>, en un solo lugar.</h2>
            <p class="text-muted mb-0">Registra, califica y organiza lo que ves. Exporta reportes en PDF y conéctate por API.</p>
        </div>
    </div>
    <div class="auth-form">
        <div class="inner">
            @include('partials.alerts')
            @yield('content')
        </div>
    </div>
</div>
@endsection
