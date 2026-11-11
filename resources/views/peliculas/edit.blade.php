@extends('layouts.app')

@section('title', 'Editar película')

@section('content')
<div class="card-pa p-4">
    <form action="{{ route('peliculas.update', $pelicula) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('peliculas._form')
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-pa"><i class="bi bi-check-lg"></i> Actualizar</button>
            <a href="{{ route('peliculas.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection
