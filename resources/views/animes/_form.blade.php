{{-- Formulario compartido por crear y editar anime --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="titulo">Título *</label>
                <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo', $anime->titulo) }}" required maxlength="150">
                @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="estudio">Estudio</label>
                <input type="text" name="estudio" id="estudio" class="form-control @error('estudio') is-invalid @enderror" value="{{ old('estudio', $anime->estudio) }}" maxlength="120">
                @error('estudio')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="genero_id">Género *</label>
                <select name="genero_id" id="genero_id" class="form-select @error('genero_id') is-invalid @enderror" required>
                    <option value="">Selecciona…</option>
                    @foreach ($generos as $g)
                        <option value="{{ $g->id }}" @selected(old('genero_id', $anime->genero_id) == $g->id)>{{ $g->nombre }}</option>
                    @endforeach
                </select>
                @error('genero_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="anio">Año *</label>
                <input type="number" name="anio" id="anio" class="form-control @error('anio') is-invalid @enderror" value="{{ old('anio', $anime->anio) }}" min="1917" max="{{ date('Y') + 5 }}" required>
                @error('anio')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="episodios">Episodios *</label>
                <input type="number" name="episodios" id="episodios" class="form-control @error('episodios') is-invalid @enderror" value="{{ old('episodios', $anime->episodios ?? 12) }}" min="1" max="5000" required>
                @error('episodios')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="estado">Estado *</label>
                <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                    @foreach (\App\Models\Anime::ESTADOS as $estado)
                        <option value="{{ $estado }}" @selected(old('estado', $anime->estado ?? 'Finalizado') === $estado)>{{ $estado }}</option>
                    @endforeach
                </select>
                @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label" for="calificacion">Calificación *</label>
                <input type="number" step="0.1" name="calificacion" id="calificacion" class="form-control @error('calificacion') is-invalid @enderror" value="{{ old('calificacion', $anime->calificacion) }}" min="0" max="10" required>
                @error('calificacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="sinopsis">Sinopsis</label>
                <textarea name="sinopsis" id="sinopsis" rows="4" class="form-control @error('sinopsis') is-invalid @enderror" maxlength="2000">{{ old('sinopsis', $anime->sinopsis) }}</textarea>
                @error('sinopsis')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        @include('partials.image-input', ['campo' => 'imagen', 'label' => 'Imagen / portada', 'actual' => $anime->imagen_url])
    </div>
</div>
