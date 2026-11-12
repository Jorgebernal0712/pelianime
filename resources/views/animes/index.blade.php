@extends('layouts.app')

@section('title', 'Animes')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <p class="text-muted mb-0">{{ $animes->count() }} animes registrados</p>
    <div class="d-flex gap-2">
        <a href="{{ route('anilist.index') }}" class="btn btn-ghost btn-sm"><i class="bi bi-cloud-download"></i> Importar de AniList</a>
        <a href="{{ route('reportes.animes') }}" class="btn btn-ghost btn-sm"><i class="bi bi-file-earmark-pdf"></i> Descargar PDF</a>
        <a href="{{ route('animes.create') }}" class="btn btn-pa btn-sm"><i class="bi bi-plus-lg"></i> Nuevo anime</a>
    </div>
</div>

<div class="card-pa p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle datatable w-100">
            <thead>
                <tr>
                    <th class="no-sort">Imagen</th>
                    <th>Título</th>
                    <th>Estudio</th>
                    <th>Género</th>
                    <th>Año</th>
                    <th>Episodios</th>
                    <th>Estado</th>
                    <th>Calificación</th>
                    <th class="no-sort text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($animes as $a)
                    <tr>
                        <td>
                            @if ($a->imagen_url)
                                <img src="{{ $a->imagen_url }}" class="thumb" alt="">
                            @else
                                <div class="thumb-empty"><i class="bi bi-stars"></i></div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $a->titulo }}</td>
                        <td>{{ $a->estudio ?? '—' }}</td>
                        <td><span class="badge badge-genre">{{ $a->genero->nombre }}</span></td>
                        <td>{{ $a->anio }}</td>
                        <td>{{ $a->episodios }}</td>
                        <td>
                            @php $color = ['En emisión' => 'success', 'Finalizado' => 'secondary', 'Próximamente' => 'warning'][$a->estado] ?? 'secondary'; @endphp
                            <span class="badge text-bg-{{ $color }}">{{ $a->estado }}</span>
                        </td>
                        <td data-order="{{ $a->calificacion }}"><span class="rating"><i class="bi bi-star-fill"></i> {{ number_format($a->calificacion, 1) }}</span></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('animes.show', $a) }}" class="btn btn-ghost btn-sm" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('animes.edit', $a) }}" class="btn btn-ghost btn-sm" title="Editar"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('animes.destroy', $a) }}" method="POST" class="d-inline form-eliminar" data-nombre="{{ $a->titulo }}">
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
