@extends('layouts.auth')

@section('title', 'Crear cuenta')

@section('content')
    <h1 class="h3 fw-bold mb-1">Crear cuenta</h1>
    <p class="text-muted mb-4">Empieza a armar tu catálogo en menos de un minuto.</p>

    <form method="POST" action="{{ route('register.post') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nombre completo</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                   class="form-control @error('name') is-invalid @enderror" placeholder="Tu nombre">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="form-control @error('email') is-invalid @enderror" placeholder="tucorreo@ejemplo.com">
        </div>
        <div class="row g-3 mb-1">
            <div class="col-sm-6">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" required class="form-control @error('password') is-invalid @enderror">
            </div>
            <div class="col-sm-6">
                <label for="password_confirmation" class="form-label">Confirmar</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="form-control">
            </div>
        </div>
        <div class="form-text mb-4">Mínimo 8 caracteres, con letras y números.</div>

        <button type="submit" class="btn btn-pa w-100 py-2">Crear cuenta</button>
    </form>

    <p class="text-center text-muted small mt-4 mb-0">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
@endsection
