@extends('layouts.pdf')

@section('title', 'Reporte de animes')

@section('content')
<h1>Catálogo de animes</h1>
<div class="resumen">
    <span>Total: <b>{{ $registros->count() }}</b></span>
    <span>Calificación promedio: <b>{{ $promedio }}</b></span>
    @foreach ($registros->groupBy('estado') as $estado => $grupo)
        <span>{{ $estado }}: <b>{{ $grupo->count() }}</b></span>
    @endforeach
</div>
<table>
    <thead>
        <tr><th>#</th><th>Título</th><th>Estudio</th><th>Género</th><th class="num">Año</th><th class="num">Episodios</th><th>Estado</th><th class="num">Calificación</th></tr>
    </thead>
    <tbody>
        @foreach ($registros as $i => $a)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><b>{{ $a->titulo }}</b></td>
                <td>{{ $a->estudio ?? '—' }}</td>
                <td>{{ $a->genero->nombre }}</td>
                <td class="num">{{ $a->anio }}</td>
                <td class="num">{{ $a->episodios }}</td>
                <td>{{ $a->estado }}</td>
                <td class="num estrella">{{ number_format($a->calificacion, 1) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
