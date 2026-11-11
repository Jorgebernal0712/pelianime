{{-- Formulario compartido por crear y editar película --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="titulo">Título *</label>
                <input type="text" name="titulo" id="titulo" class="form-control @error('titulo') is-invalid @enderror" value="{{ old('titulo', $pelicula->titulo) }}" required maxlength="150">
                @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="director">Director</label>
                <input type="text" name="director" id="director" class="form-control @error('director') is-invalid @enderror" value="{{ old('director', $pelicula->director) }}" maxlength="120">
                @error('director')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="genero_id">Género *</label>
                <select name="genero_id" id="genero_id" class="form-select @error('genero_id') is-invalid @enderror" required>
                    <option value="">Selecciona…</option>
                    @foreach ($generos as $g)
                        <option value="{{ $g->id }}" @selected(old('genero_id', $pelicula->genero_id) == $g->id)>{{ $g->nombre }}</option>
                    @endforeach
                </select>
                @error('genero_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="anio">Año *</label>
                <input type="number" name="anio" id="anio" class="form-control @error('anio') is-invalid @enderror" value="{{ old('anio', $pelicula->anio) }}" min="1888" max="{{ date('Y') + 5 }}" required>
                @error('anio')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="duracion_min">Duración (min)</label>
                <input type="number" name="duracion_min" id="duracion_min" class="form-control @error('duracion_min') is-invalid @enderror" value="{{ old('duracion_min', $pelicula->duracion_min) }}" min="1" max="600">
                @error('duracion_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="calificacion">Calificación (0-10) *</label>
                <input type="number" step="0.1" name="calificacion" id="calificacion" class="form-control @error('calificacion') is-invalid @enderror" value="{{ old('calificacion', $pelicula->calificacion) }}" min="0" max="10" required>
                @error('calificacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="sinopsis">Sinopsis</label>
                <textarea name="sinopsis" id="sinopsis" rows="4" class="form-control @error('sinopsis') is-invalid @enderror" maxlength="2000">{{ old('sinopsis', $pelicula->sinopsis) }}</textarea>
                @error('sinopsis')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        @include('partials.image-input', ['campo' => 'poster', 'label' => 'Póster', 'actual' => $pelicula->poster_url])
    </div>
</div>
