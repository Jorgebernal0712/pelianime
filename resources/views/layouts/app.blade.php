{{-- ============================================================
     MAESTRA DEL PANEL (dashboard): menú lateral + barra superior.
     Hereda de layouts.base. Las vistas privadas extienden esta.
     ============================================================ --}}
@extends('layouts.base')

@section('body')
<div class="app-shell">
    {{-- ---------- Menú lateral ---------- --}}
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="brand"><span class="text-gradient">●</span> PeliAnime</a>

        <div class="menu-title">General</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2"></i> Dashboard</a>
        </nav>

        <div class="menu-title">Catálogo</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('peliculas.*') ? 'active' : '' }}" href="{{ route('peliculas.index') }}"><i class="bi bi-film"></i> Listar películas</a>
            <a class="nav-link {{ request()->routeIs('animes.*') ? 'active' : '' }}" href="{{ route('animes.index') }}"><i class="bi bi-stars"></i> Listar animes</a>
        </nav>

        <div class="menu-title">Reportes</div>
        <nav class="nav flex-column">
            <a class="nav-link" href="{{ route('reportes.peliculas') }}"><i class="bi bi-file-earmark-pdf"></i> PDF películas</a>
            <a class="nav-link" href="{{ route('reportes.animes') }}"><i class="bi bi-file-earmark-pdf"></i> PDF animes</a>
        </nav>

        <div class="menu-title">Servicios web</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('cliente-api.*') ? 'active' : '' }}" href="{{ route('cliente-api.index') }}"><i class="bi bi-plug"></i> Cliente API REST</a>
            <a class="nav-link {{ request()->routeIs('anilist.*') ? 'active' : '' }}" href="{{ route('anilist.index') }}"><i class="bi bi-cloud-download"></i> Importar de AniList</a>
        </nav>

        <div class="menu-title">Mi cuenta</div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}"><i class="bi bi-person-gear"></i> Editar perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
            </form>
        </nav>

        <div class="user-box d-flex align-items-center gap-2">
            <img src="{{ auth()->user()->avatar_url }}" class="avatar" alt="avatar">
            <div class="small text-truncate">
                <div class="fw-semibold text-truncate">{{ auth()->user()->name }}</div>
                <div class="text-muted text-truncate">{{ auth()->user()->email }}</div>
            </div>
        </div>
    </aside>

    {{-- ---------- Contenido ---------- --}}
    <div class="main">
        <header class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-ghost btn-sm d-lg-none" id="toggleSidebar" aria-label="Menú"><i class="bi bi-list"></i></button>
                <h1 class="h5 mb-0">@yield('title')</h1>
            </div>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none text-reset" data-bs-toggle="dropdown">
                    <span class="d-none d-sm-inline small text-muted">Hola, <strong class="text-white">{{ auth()->user()->name }}</strong></span>
                    <img src="{{ auth()->user()->avatar_url }}" class="avatar" alt="avatar">
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person-gear me-2"></i>Editar perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

        <main class="content">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('toggleSidebar')?.addEventListener('click', e => {
        e.stopPropagation();
        document.getElementById('sidebar').classList.toggle('open');
    });
    document.addEventListener('click', e => {
        const s = document.getElementById('sidebar');
        if (s.classList.contains('open') && !s.contains(e.target)) s.classList.remove('open');
    });
</script>
@endpush
@endsection
