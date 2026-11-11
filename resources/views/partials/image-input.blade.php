{{-- Campo para subir imagen con vista previa. Variables: $campo, $label, $actual --}}
<label class="form-label" for="{{ $campo }}">{{ $label }}</label>
<div class="card-pa p-3 text-center">
    <img id="preview-{{ $campo }}" src="{{ $actual ?? '' }}" class="img-preview mb-3 {{ $actual ? '' : 'd-none' }}" alt="vista previa">
    <div id="empty-{{ $campo }}" class="text-muted small mb-3 {{ $actual ? 'd-none' : '' }}"><i class="bi bi-image fs-1 d-block"></i>Sin imagen</div>
    <input type="file" name="{{ $campo }}" id="{{ $campo }}" accept="image/png,image/jpeg,image/webp"
           class="form-control form-control-sm @error($campo) is-invalid @enderror">
    @error($campo)<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="form-text">JPG, PNG o WEBP · máx. 2 MB</div>
</div>
@push('scripts')
<script>
    document.getElementById('{{ $campo }}').addEventListener('change', function () {
        const [file] = this.files;
        if (!file) return;
        const img = document.getElementById('preview-{{ $campo }}');
        img.src = URL.createObjectURL(file);
        img.classList.remove('d-none');
        document.getElementById('empty-{{ $campo }}').classList.add('d-none');
    });
</script>
@endpush
