<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Validación compartida por el CRUD web y por la API de películas. */
class PeliculaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        // En una actualización por API (PUT) los campos son opcionales
        $req = $this->isMethod('put') && $this->is('api/*') ? 'sometimes' : 'required';

        return [
            'titulo' => [$req, 'string', 'max:150'],
            'director' => ['nullable', 'string', 'max:120'],
            'genero_id' => [$req, 'integer', 'exists:generos,id'],
            'anio' => [$req, 'integer', 'min:1888', 'max:'.(date('Y') + 5)],
            'duracion_min' => ['nullable', 'integer', 'min:1', 'max:600'],
            'calificacion' => [$req, 'numeric', 'min:0', 'max:10'],
            'sinopsis' => ['nullable', 'string', 'max:2000'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return ['genero_id' => 'género', 'anio' => 'año', 'duracion_min' => 'duración', 'calificacion' => 'calificación'];
    }
}
