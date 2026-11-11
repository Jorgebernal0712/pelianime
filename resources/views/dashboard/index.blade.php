@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
{{-- Identificación del usuario activo --}}
<div class="card-pa p-4 mb-4 d-flex flex-column flex-md-row align-items-md-center gap-3" style="background: radial-gradient(circle at 100% 0, rgba(139,92,246,.25), transparent 50%), var(--pa-surface)">
    <img src="{{ auth()->user()->avatar_url }}" class="avatar" style="width:64px;height:64px" alt="avatar">
    <div class="flex-grow-1">
        <div class="text-muted small">Sesión iniciada como</div>
        <h2 class="h4 fw-bold mb-0">{{ auth()->user()->name }}</h2>
        <div class="small text-muted">{{ auth()->user()->email }} · miembro desde {{ auth()->user()->created_at->translatedFormat('F Y') }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('peliculas.create') }}" class="btn btn-pa btn-sm"><i class="bi bi-plus-lg"></i> Película</a>
        <a href="{{ route('animes.create') }}" class="btn btn-ghost btn-sm"><i class="bi bi-plus-lg"></i> Anime</a>
    </div>
</div>

{{-- Indicadores --}}
<div class="row g-3 mb-4">
    @foreach ([
        ['Películas', $totalPeliculas, 'bi-film', 'bg-soft-violet'],
        ['Animes', $totalAnimes, 'bi-stars', 'bg-soft-rose'],
        ['Promedio películas', $promPeliculas, 'bi-star-half', 'bg-soft-amber'],
        ['Promedio animes', $promAnimes, 'bi-star-half', 'bg-soft-cyan'],
    ] as [$label, $valor, $icon, $bg])
        <div class="col-6 col-xl-3">
            <div class="card-pa stat">
                <div class="icon {{ $bg }}"><i class="bi {{ $icon }}"></i></div>
                <div><div class="value">{{ $valor }}</div><div class="small text-muted">{{ $label }}</div></div>
            </div>
        </div>
    @endforeach
</div>

{{-- Gráficas (Chart.js) --}}
<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="card-pa p-4 h-100">
            <h3 class="h6 fw-bold mb-3">Títulos por género</h3>
            <div style="height: 320px"><canvas id="chartGeneros"></canvas></div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card-pa p-4 h-100">
            <h3 class="h6 fw-bold mb-3">Animes por estado</h3>
            <div style="height: 320px"><canvas id="chartEstados"></canvas></div>
        </div>
    </div>
</div>

{{-- Últimos registros --}}
<div class="row g-3">
    @foreach ([['Últimas películas', $ultimasPeliculas, 'peliculas'], ['Últimos animes', $ultimosAnimes, 'animes']] as [$titulo, $lista, $ruta])
        <div class="col-lg-6">
            <div class="card-pa p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 fw-bold mb-0">{{ $titulo }}</h3>
                    <a href="{{ route($ruta.'.index') }}" class="small">Ver todos</a>
                </div>
                <ul class="list-unstyled mb-0">
                    @forelse ($lista as $item)
                        <li class="d-flex justify-content-between py-2 border-bottom" style="border-color: var(--pa-border) !important">
                            <a href="{{ route($ruta.'.show', $item) }}" class="text-reset text-decoration-none">{{ $item->titulo }} <span class="text-muted small">({{ $item->anio }})</span></a>
                            <span class="rating small"><i class="bi bi-star-fill"></i> {{ number_format($item->calificacion, 1) }}</span>
                        </li>
                    @empty
                        <li class="text-muted small">Aún no hay registros.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.color = '#9a93b0';
    Chart.defaults.borderColor = '#2c2640';
    Chart.defaults.font.family = "'DM Sans', sans-serif";

    const datos = @json($grafica);

    new Chart(document.getElementById('chartGeneros'), {
        type: 'bar',
        data: {
            labels: datos.labels,
            datasets: [
                { label: 'Películas', data: datos.peliculas, backgroundColor: '#8b5cf6', borderRadius: 6 },
                { label: 'Animes', data: datos.animes, backgroundColor: '#f43f5e', borderRadius: 6 },
            ],
        },
        options: {
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } },
            plugins: { legend: { position: 'bottom' } },
        },
    });

    const estados = @json($estados);
    new Chart(document.getElementById('chartEstados'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(estados),
            datasets: [{ data: Object.values(estados), backgroundColor: ['#8b5cf6', '#f43f5e', '#fbbf24'], borderColor: '#16131f', borderWidth: 3 }],
        },
        options: { maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom' } } },
    });
</script>
@endpush
