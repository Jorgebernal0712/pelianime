<?php

namespace Database\Seeders;

use App\Models\Genero;
use App\Models\Pelicula;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeliculaSeeder extends Seeder
{
    public function run(): void
    {
        $g = Genero::pluck('id', 'nombre');
        $userId = User::first()?->id;

        // [titulo, director, genero, año, duración, calificación]
        $datos = [
            ['Inception', 'Christopher Nolan', 'Ciencia ficción', 2010, 148, 8.8],
            ['Interstellar', 'Christopher Nolan', 'Ciencia ficción', 2014, 169, 8.7],
            ['El Padrino', 'Francis Ford Coppola', 'Drama', 1972, 175, 9.2],
            ['Parasite', 'Bong Joon-ho', 'Suspenso', 2019, 132, 8.5],
            ['Mad Max: Fury Road', 'George Miller', 'Acción', 2015, 120, 8.1],
            ['El viaje de Chihiro', 'Hayao Miyazaki', 'Animación', 2001, 125, 8.6],
            ['Coco', 'Lee Unkrich', 'Animación', 2017, 105, 8.4],
            ['El conjuro', 'James Wan', 'Terror', 2013, 112, 7.5],
            ['La La Land', 'Damien Chazelle', 'Romance', 2016, 128, 8.0],
            ['Superbad', 'Greg Mottola', 'Comedia', 2007, 113, 7.6],
            ['El señor de los anillos: La comunidad del anillo', 'Peter Jackson', 'Fantasía', 2001, 178, 8.9],
            ['Jurassic Park', 'Steven Spielberg', 'Aventura', 1993, 127, 8.2],
        ];

        foreach ($datos as [$titulo, $director, $genero, $anio, $duracion, $calif]) {
            Pelicula::firstOrCreate(['titulo' => $titulo], [
                'director' => $director,
                'genero_id' => $g[$genero],
                'anio' => $anio,
                'duracion_min' => $duracion,
                'calificacion' => $calif,
                'sinopsis' => "Película de {$genero} dirigida por {$director}.",
                'user_id' => $userId,
            ]);
        }
    }
}
