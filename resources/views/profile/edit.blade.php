@extends('layouts.app')

@section('title', 'Editar perfil')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-pa p-4">
            <h2 class="h6 fw-bold mb-3">Información personal</h2>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row g-4">
                    <div class="col-md-4 text-center">
                        <img id="preview-avatar" src="{{ $user->avatar_url }}" class="avatar avatar-lg mb-3" alt="avatar">
                        <input type="file" name="avatar" id="avatar" accept="image/png,image/jpeg,image/webp" class="form-control form-control-sm @error('avatar') is-invalid @enderror">
                        @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Foto de perfil · máx. 2 MB</div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nombre</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="email">Correo electrónico</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="telefono">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $user->telefono) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="bio">Biografía</label>
                            <textarea id="bio" name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror" maxlength="500" placeholder="Cuéntanos qué te gusta ver…">{{ old('bio', $user->bio) }}</textarea>
                        </div>
                        <button class="btn btn-pa"><i class="bi bi-check-lg"></i> Guardar cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-pa p-4">
            <h2 class="h6 fw-bold mb-3">Cambiar contraseña</h2>
            <form action="{{ route('profile.password') }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="password_actual">Contraseña actual</label>
                    <input type="password" id="password_actual" name="password_actual" class="form-control @error('password_actual') is-invalid @enderror" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Nueva contraseña</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Confirmar nueva contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                </div>
                <button class="btn btn-ghost w-100"><i class="bi bi-shield-lock"></i> Actualizar contraseña</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('avatar').addEventListener('change', function () {
        if (this.files[0]) document.getElementById('preview-avatar').src = URL.createObjectURL(this.files[0]);
    });
</script>
@endpush
