<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Pelicula extends Model
{
    use HasFactory;

    protected $table = 'peliculas';

    protected $fillable = [
        'titulo', 'director', 'genero_id', 'anio', 'duracion_min',
        'calificacion', 'sinopsis', 'poster', 'user_id',
    ];

    protected $casts = ['calificacion' => 'float', 'anio' => 'integer', 'duracion_min' => 'integer'];

    protected $appends = ['poster_url'];

    public function genero(): BelongsTo
    {
        return $this->belongsTo(Genero::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPosterUrlAttribute(): ?string
    {
        if (! $this->poster) {
            return null;
        }

        return str_starts_with($this->poster, 'http') ? $this->poster : Storage::url($this->poster);
    }
}
