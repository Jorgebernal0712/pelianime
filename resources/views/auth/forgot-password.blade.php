@extends('layouts.auth')

@section('title', 'Recuperar contraseña')

@section('content')
    <a href="{{ route('login') }}" class="small text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Volver</a>
    <h1 class="h3 fw-bold mt-3 mb-1">¿Olvidaste tu contraseña?</h1>
    <p class="text-muted mb-4">Escribe tu correo y te enviaremos un enlace para crear una nueva.</p>

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf
        <div class="mb-4">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                   class="form-control @error('email') is-invalid @enderror" placeholder="tucorreo@ejemplo.com">
        </div>
        <button type="submit" class="btn btn-pa w-100 py-2"><i class="bi bi-send me-1"></i> Enviar enlace</button>
    </form>
@endsection
