@extends('layouts.auth')

@section('title', 'Iniciar sesión')

@section('content')
    <h1 class="h3 fw-bold mb-1">Iniciar sesión</h1>
    <p class="text-muted mb-4">Bienvenido de nuevo. Ingresa tus datos para continuar.</p>

    <form method="POST" action="{{ route('login.post') }}" novalidate>
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       class="form-control @error('email') is-invalid @enderror" placeholder="tucorreo@ejemplo.com">
            </div>
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <label for="password" class="form-label">Contraseña</label>
                <a href="{{ route('password.request') }}" class="small">¿Olvidaste tu contraseña?</a>
            </div>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" id="password" name="password" required
                       class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                <button class="btn btn-ghost" type="button" onclick="const i=document.getElementById('password'); i.type = i.type==='password'?'text':'password'"><i class="bi bi-eye"></i></button>
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label small" for="remember">Mantener sesión iniciada</label>
        </div>

        <button type="submit" class="btn btn-pa w-100 py-2">Entrar</button>
    </form>

    <p class="text-center text-muted small mt-4 mb-0">¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate gratis</a></p>
@endsection
