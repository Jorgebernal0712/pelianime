@extends('layouts.app')

@section('title', 'Películas')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <p class="text-muted mb-0">{{ $peliculas->count() }} películas registradas</p>
    <div class="d-flex gap-2">
        <a href="{{ route('reportes.peliculas') }}" class="btn btn-ghost btn-sm"><i class="bi bi-file-earmark-pdf"></i> Descargar PDF</a>
        <a href="{{ route('peliculas.create') }}" class="btn btn-pa btn-sm"><i class="bi bi-plus-lg"></i> Nueva película</a>
    </div>
</div>

<div class="card-pa p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable w-100">
            <thead>
                <tr>
                    <th class="no-sort">Póster</th>
                    <th>Título</th>
                    <th>Director</th>
                    <th>Género</th>
                    <th>Año</th>
                    <th>Duración</th>
                    <th>Calificación</th>
                    <th class="no-sort text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($peliculas as $p)
                    <tr>
                        <td>
                            @if ($p->poster_url)
                                <img src="{{ $p->poster_url }}" class="thumb" alt="">
                            @else
                                <div class="thumb-empty"><i class="bi bi-film"></i></div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $p->titulo }}</td>
                        <td>{{ $p->director ?? '—' }}</td>
                        <td><span class="badge badge-genre">{{ $p->genero->nombre }}</span></td>
                        <td>{{ $p->anio }}</td>
                        <td data-order="{{ $p->duracion_min }}">{{ $p->duracion_min ? $p->duracion_min.' min' : '—' }}</td>
                        <td data-order="{{ $p->calificacion }}"><span class="rating"><i class="bi bi-star-fill"></i> {{ number_format($p->calificacion, 1) }}</span></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('peliculas.show', $p) }}" class="btn btn-ghost btn-sm" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('peliculas.edit', $p) }}" class="btn btn-ghost btn-sm" title="Editar"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('peliculas.destroy', $p) }}" method="POST" class="d-inline form-eliminar" data-nombre="{{ $p->titulo }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-ghost btn-sm text-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@include('partials.datatables')
