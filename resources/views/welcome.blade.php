{{-- LANDING PAGE (página de inicio pública). Hereda de layouts.base --}}
@extends('layouts.base')

@section('title', 'Bienvenido')

@section('body')
<nav class="landing-nav">
    <div class="container d-flex align-items-center justify-content-between py-3">
        <a href="{{ route('home') }}" class="brand fs-4 fw-bold text-white text-decoration-none"><span class="text-gradient">●</span> PeliAnime</a>
        <div class="d-flex gap-2">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-pa btn-sm px-3">Ir al panel</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm px-3">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="btn btn-pa btn-sm px-3">Crear cuenta</a>
            @endauth
        </div>
    </div>
</nav>

<section class="hero">
    <div class="container position-relative" style="z-index:1">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge badge-genre rounded-pill px-3 py-2 mb-3"><i class="bi bi-lightning-charge-fill text-warning"></i> Proyecto Laravel 12</span>
                <h1 class="mb-4">Todo lo que ves,<br><span class="text-gradient">bien organizado.</span></h1>
                <p class="lead text-muted mb-4">Administra tu colección de películas y animes: califica, sube pósters, mira estadísticas, descarga reportes en PDF e intégrala con otras aplicaciones mediante nuestra API REST.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('register') }}" class="btn btn-pa btn-lg px-4">Empezar gratis</a>
                    <a href="#destacados" class="btn btn-ghost btn-lg px-4">Ver destacados</a>
                </div>
                <div class="d-flex gap-4 mt-5">
                    <div><div class="h3 fw-bold mb-0">{{ $totalPeliculas }}</div><div class="small text-muted">películas</div></div>
                    <div><div class="h3 fw-bold mb-0">{{ $totalAnimes }}</div><div class="small text-muted">animes</div></div>
                    <div><div class="h3 fw-bold mb-0">API</div><div class="small text-muted">REST con tokens</div></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    @foreach ($destacadasPeliculas->take(2)->concat($destacadosAnimes->take(1)) as $i => $item)
                        <div class="col-4" style="margin-top: {{ [2, 0, 3][$i] ?? 0 }}rem">
                            @include('partials.poster', [
                                'titulo' => $item->titulo,
                                'imagen' => $item->poster_url ?? $item->imagen_url,
                                'sub' => $item->anio,
                                'calificacion' => $item->calificacion,
                                'icono' => $item instanceof \App\Models\Anime ? 'bi-stars' : 'bi-film',
                            ])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-3">
            @foreach ([
                ['bi-collection-play', 'bg-soft-violet', 'CRUD completo', 'Crea, consulta, edita y elimina películas y animes con imágenes.'],
                ['bi-bar-chart-line', 'bg-soft-rose', 'Estadísticas', 'Gráficas por género y estado directamente en tu panel.'],
                ['bi-file-earmark-pdf', 'bg-soft-amber', 'Reportes PDF', 'Descarga el catálogo completo listo para imprimir.'],
                ['bi-plug', 'bg-soft-cyan', 'API REST', 'Conecta apps externas usando tokens Bearer con Sanctum.'],
            ] as [$icon, $bg, $titulo, $texto])
                <div class="col-sm-6 col-lg-3">
                    <div class="card-pa feature">
                        <div class="icon {{ $bg }}"><i class="bi {{ $icon }}"></i></div>
                        <h3 class="h6 fw-bold">{{ $titulo }}</h3>
                        <p class="small text-muted mb-0">{{ $texto }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-5" id="destacados">
    <div class="container">
        <h2 class="h3 fw-bold mb-4"><i class="bi bi-film text-gradient"></i> Películas mejor calificadas</h2>
        <div class="row g-3 mb-5">
            @foreach ($destacadasPeliculas as $p)
                <div class="col-6 col-md-3">
                    @include('partials.poster', ['titulo' => $p->titulo, 'imagen' => $p->poster_url, 'sub' => $p->genero->nombre.' · '.$p->anio, 'calificacion' => $p->calificacion, 'icono' => 'bi-film'])
                </div>
            @endforeach
        </div>

        <h2 class="h3 fw-bold mb-4"><i class="bi bi-stars text-gradient"></i> Animes mejor calificados</h2>
        <div class="row g-3">
            @foreach ($destacadosAnimes as $a)
                <div class="col-6 col-md-3">
                    @include('partials.poster', ['titulo' => $a->titulo, 'imagen' => $a->imagen_url, 'sub' => $a->episodios.' eps · '.$a->anio, 'calificacion' => $a->calificacion, 'icono' => 'bi-stars'])
                </div>
            @endforeach
        </div>
    </div>
</section>

<footer class="border-top py-4 mt-4" style="border-color: var(--pa-border) !important">
    <div class="container d-flex flex-wrap justify-content-between small text-muted gap-2">
        <span>© {{ date('Y') }} PeliAnime · Proyecto académico en Laravel {{ app()->version() }}</span>
        <span>Equipo: Integrante 1 · Integrante 2 · Integrante 3</span>
    </div>
</footer>
@endsection
