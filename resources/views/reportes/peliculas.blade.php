@extends('layouts.pdf')

@section('title', 'Reporte de películas')

@section('content')
<h1>Catálogo de películas</h1>
<div class="resumen">
    <span>Total: <b>{{ $registros->count() }}</b></span>
    <span>Calificación promedio: <b>{{ $promedio }}</b></span>
</div>
<table>
    <thead>
        <tr><th>#</th><th>Título</th><th>Director</th><th>Género</th><th class="num">Año</th><th class="num">Duración</th><th class="num">Calificación</th></tr>
    </thead>
    <tbody>
        @foreach ($registros as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><b>{{ $p->titulo }}</b></td>
                <td>{{ $p->director ?? '—' }}</td>
                <td>{{ $p->genero->nombre }}</td>
                <td class="num">{{ $p->anio }}</td>
                <td class="num">{{ $p->duracion_min ? $p->duracion_min.' min' : '—' }}</td>
                <td class="num estrella">{{ number_format($p->calificacion, 1) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
