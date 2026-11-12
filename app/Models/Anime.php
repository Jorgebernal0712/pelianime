<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Anime extends Model
{
    use HasFactory;

    public const ESTADOS = ['En emisión', 'Finalizado', 'Próximamente'];

    protected $table = 'animes';

    protected $fillable = [
        'titulo', 'estudio', 'genero_id', 'anio', 'episodios', 'estado',
        'calificacion', 'sinopsis', 'imagen', 'user_id',
    ];

    protected $casts = ['calificacion' => 'float', 'anio' => 'integer', 'episodios' => 'integer'];

    protected $appends = ['imagen_url'];

    public function genero(): BelongsTo
    {
        return $this->belongsTo(Genero::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getImagenUrlAttribute(): ?string
    {
        if (! $this->imagen) {
            return null;
        }

        // Puede ser un archivo subido o una URL externa (importado desde AniList)
        if (str_starts_with($this->imagen, 'http')) {
            return $this->imagen;                       // URL externa (p. ej. importado de AniList)
        }
        if (str_starts_with($this->imagen, 'img/')) {
            return asset($this->imagen);                // imagen de ejemplo incluida en public/img
        }

        return Storage::url($this->imagen);             // imagen subida por el usuario
    }
}
