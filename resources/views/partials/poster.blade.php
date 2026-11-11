{{-- Tarjeta de póster reutilizable. Variables: $titulo, $imagen, $sub, $calificacion, $icono --}}
<div class="poster-card">
    @if ($imagen)
        <img src="{{ $imagen }}" alt="{{ $titulo }}" loading="lazy">
    @else
        <div class="placeholder-art"><i class="bi {{ $icono ?? 'bi-film' }}"></i></div>
    @endif
    <div class="info">
        <div class="fw-semibold small lh-sm">{{ $titulo }}</div>
        <div class="d-flex justify-content-between small mt-1">
            <span class="text-muted">{{ $sub }}</span>
            <span class="rating"><i class="bi bi-star-fill"></i> {{ number_format($calificacion, 1) }}</span>
        </div>
    </div>
</div>
