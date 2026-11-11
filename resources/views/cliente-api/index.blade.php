@extends('layouts.app')

@section('title', 'Cliente API REST')

@section('content')
{{-- Explicación + token --}}
<div class="card-pa p-4 mb-4">
    <div class="row g-3 align-items-center">
        <div class="col-lg-8">
            <h2 class="h6 fw-bold">¿Qué hace esta pantalla?</h2>
            <p class="small text-muted mb-2">
                El servidor de Laravel actúa como <b>cliente HTTP</b> y consume nuestra propia API REST (<code>{{ $baseUrl }}</code>)
                igual que lo haría una aplicación externa. Cada petición viaja con un <b>token Bearer</b> de Sanctum y las cabeceras
                <code>Accept: application/json</code> y <code>X-Cliente</code>.
            </p>
            <div class="d-flex flex-wrap gap-2 small">
                <span class="method method-GET">GET</span> listar
                <span class="method method-POST">POST</span> crear
                <span class="method method-PUT">PUT</span> actualizar
                <span class="method method-DELETE">DELETE</span> eliminar
            </div>
        </div>
        <div class="col-lg-4">
            <label class="form-label small text-muted mb-1">Token de acceso (Sanctum)</label>
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-key"></i></span>
                <input type="text" class="form-control font-monospace" value="{{ $tokenMascara }}" readonly>
            </div>
            <form action="{{ route('cliente-api.token') }}" method="POST" class="mt-2">@csrf
                <button class="btn btn-ghost btn-sm w-100"><i class="bi bi-arrow-repeat"></i> Revocar y generar nuevo token</button>
            </form>
        </div>
    </div>
</div>

@if ($error)
    <div class="alert alert-warning"><i class="bi bi-wifi-off"></i> {{ $error }}</div>
@endif

<div class="row g-4">
    {{-- Formularios POST / PUT --}}
    <div class="col-xl-5">
        <div class="card-pa p-4 mb-4">
            <h3 class="h6 fw-bold"><span class="method method-POST">POST</span> /api/peliculas</h3>
            <form action="{{ route('cliente-api.store') }}" method="POST" class="row g-2 mt-1">
                @csrf
                <div class="col-12"><input name="titulo" class="form-control form-control-sm" placeholder="Título *" required></div>
                <div class="col-6"><input name="director" class="form-control form-control-sm" placeholder="Director"></div>
                <div class="col-6">
                    <select name="genero_id" class="form-select form-select-sm" required>
                        @foreach ($generos as $g)<option value="{{ $g->id }}">{{ $g->nombre }}</option>@endforeach
                    </select>
                </div>
                <div class="col-4"><input name="anio" type="number" class="form-control form-control-sm" placeholder="Año *" required></div>
                <div class="col-4"><input name="duracion_min" type="number" class="form-control form-control-sm" placeholder="Min"></div>
                <div class="col-4"><input name="calificacion" type="number" step="0.1" min="0" max="10" class="form-control form-control-sm" placeholder="Calif. *" required></div>
                <div class="col-12"><button class="btn btn-pa btn-sm w-100">Enviar POST</button></div>
            </form>
        </div>

        <div class="card-pa p-4">
            <h3 class="h6 fw-bold"><span class="method method-PUT">PUT</span> /api/peliculas/{id}</h3>
            <form id="formPut" method="POST" class="row g-2 mt-1">
                @csrf @method('PUT')
                <div class="col-12">
                    <select id="putId" class="form-select form-select-sm" required>
                        <option value="">Selecciona la película…</option>
                        @foreach ($peliculas as $p)<option value="{{ $p['id'] }}">#{{ $p['id'] }} · {{ $p['titulo'] }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12"><input name="titulo" class="form-control form-control-sm" placeholder="Nuevo título (opcional)"></div>
                <div class="col-6"><input name="anio" type="number" class="form-control form-control-sm" placeholder="Año (opcional)"></div>
                <div class="col-6"><input name="calificacion" type="number" step="0.1" min="0" max="10" class="form-control form-control-sm" placeholder="Calificación"></div>
                <div class="col-12"><button class="btn btn-ghost btn-sm w-100">Enviar PUT</button></div>
            </form>
        </div>
    </div>

    {{-- Resultado GET + DELETE --}}
    <div class="col-xl-7">
        <div class="card-pa p-4 mb-4">
            <h3 class="h6 fw-bold mb-3"><span class="method method-GET">GET</span> /api/peliculas <span class="text-muted fw-normal small">({{ count($peliculas) }} resultados)</span></h3>
            <div class="table-responsive" style="max-height: 330px">
                <table class="table table-sm align-middle mb-0">
                    <thead><tr><th>ID</th><th>Título</th><th>Género</th><th>Calif.</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($peliculas as $p)
                            <tr>
                                <td>{{ $p['id'] }}</td>
                                <td>{{ $p['titulo'] }} <span class="text-muted small">({{ $p['anio'] }})</span></td>
                                <td class="small">{{ $p['genero']['nombre'] ?? '' }}</td>
                                <td class="rating small">{{ $p['calificacion'] }}</td>
                                <td class="text-end">
                                    <form action="{{ route('cliente-api.destroy', $p['id']) }}" method="POST" onsubmit="return confirm('¿Enviar DELETE para «{{ addslashes($p['titulo']) }}»?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm py-0 method method-DELETE border-0">DELETE</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted small">Sin datos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($ultima)
            <div class="card-pa p-4">
                <h3 class="h6 fw-bold mb-3">Última petición</h3>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="method method-{{ $ultima['metodo'] }}">{{ $ultima['metodo'] }}</span>
                    <code class="small">{{ $ultima['url'] }}</code>
                    <span class="badge text-bg-{{ $ultima['estado'] < 300 ? 'success' : 'danger' }}">HTTP {{ $ultima['estado'] }}</span>
                </div>
                <div class="row g-2">
                    <div class="col-md-5">
                        <div class="small text-muted mb-1">Cabeceras enviadas</div>
<pre class="code">@foreach ($ultima['cabeceras'] as $k => $v){{ $k }}: {{ $v }}
@endforeach
@if (!empty($ultima['cuerpo']))
Cuerpo:
{{ json_encode($ultima['cuerpo'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}@endif</pre>
                    </div>
                    <div class="col-md-7">
                        <div class="small text-muted mb-1">Respuesta JSON</div>
                        <pre class="code">{{ $ultima['respuesta'] }}</pre>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Arma la URL del PUT con el id elegido
    document.getElementById('formPut').addEventListener('submit', function (e) {
        const id = document.getElementById('putId').value;
        if (!id) { e.preventDefault(); return alert('Selecciona una película'); }
        this.action = "{{ url('cliente-api/peliculas') }}/" + id;
    });
</script>
@endpush
