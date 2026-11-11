<?php

namespace App\Http\Requests;

use App\Models\Anime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Validación compartida por el CRUD web y por la API de animes. */
class AnimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $req = $this->isMethod('put') && $this->is('api/*') ? 'sometimes' : 'required';

        return [
            'titulo' => [$req, 'string', 'max:150'],
            'estudio' => ['nullable', 'string', 'max:120'],
            'genero_id' => [$req, 'integer', 'exists:generos,id'],
            'anio' => [$req, 'integer', 'min:1917', 'max:'.(date('Y') + 5)],
            'episodios' => [$req, 'integer', 'min:1', 'max:5000'],
            'estado' => [$req, Rule::in(Anime::ESTADOS)],
            'calificacion' => [$req, 'numeric', 'min:0', 'max:10'],
            'sinopsis' => ['nullable', 'string', 'max:2000'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return ['genero_id' => 'género', 'anio' => 'año', 'calificacion' => 'calificación'];
    }
}
