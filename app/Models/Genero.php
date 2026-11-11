<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Genero extends Model
{
    protected $table = 'generos';

    protected $fillable = ['nombre'];

    public function peliculas(): HasMany
    {
        return $this->hasMany(Pelicula::class);
    }

    public function animes(): HasMany
    {
        return $this->hasMany(Anime::class);
    }
}
