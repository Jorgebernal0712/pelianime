@extends('layouts.app')

@section('title', 'Nuevo anime')

@section('content')
<div class="card-pa p-4">
    <form action="{{ route('animes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('animes._form')
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-pa"><i class="bi bi-check-lg"></i> Guardar</button>
            <a href="{{ route('animes.index') }}" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>
@endsection
