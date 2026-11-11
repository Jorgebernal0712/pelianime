@extends('layouts.auth')

@section('title', 'Nueva contraseña')

@section('content')
    <h1 class="h3 fw-bold mb-1">Crea una nueva contraseña</h1>
    <p class="text-muted mb-4">Elige una contraseña segura que no hayas usado antes.</p>

    <form method="POST" action="{{ route('password.update') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required
                   class="form-control @error('email') is-invalid @enderror">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Nueva contraseña</label>
            <input type="password" id="password" name="password" required autofocus class="form-control @error('password') is-invalid @enderror">
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required class="form-control">
        </div>
        <button type="submit" class="btn btn-pa w-100 py-2">Restablecer contraseña</button>
    </form>
@endsection
