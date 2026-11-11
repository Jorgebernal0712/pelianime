<?php

namespace Database\Seeders;

use App\Models\Genero;
use Illuminate\Database\Seeder;

class GeneroSeeder extends Seeder
{
    public function run(): void
    {
        $generos = ['Acción', 'Aventura', 'Ciencia ficción', 'Comedia', 'Drama',
            'Fantasía', 'Terror', 'Romance', 'Animación', 'Suspenso', 'Shōnen', 'Slice of life'];

        foreach ($generos as $nombre) {
            Genero::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
