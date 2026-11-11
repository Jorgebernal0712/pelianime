<?php

namespace Database\Seeders;

use App\Models\Anime;
use App\Models\Genero;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnimeSeeder extends Seeder
{
    public function run(): void
    {
        $g = Genero::pluck('id', 'nombre');
        $userId = User::first()?->id;

        // [titulo, estudio, genero, año, episodios, estado, calificación]
        $datos = [
            ['Fullmetal Alchemist: Brotherhood', 'Bones', 'Aventura', 2009, 64, 'Finalizado', 9.1],
            ['Attack on Titan', 'Wit Studio / MAPPA', 'Acción', 2013, 89, 'Finalizado', 9.0],
            ['Death Note', 'Madhouse', 'Suspenso', 2006, 37, 'Finalizado', 8.6],
            ['One Piece', 'Toei Animation', 'Shōnen', 1999, 1100, 'En emisión', 8.9],
            ['Naruto Shippuden', 'Pierrot', 'Shōnen', 2007, 500, 'Finalizado', 8.3],
            ['Demon Slayer', 'ufotable', 'Shōnen', 2019, 63, 'En emisión', 8.5],
            ['Jujutsu Kaisen', 'MAPPA', 'Acción', 2020, 47, 'En emisión', 8.6],
            ['Spy x Family', 'Wit Studio / CloverWorks', 'Comedia', 2022, 37, 'En emisión', 8.3],
            ['Steins;Gate', 'White Fox', 'Ciencia ficción', 2011, 24, 'Finalizado', 9.0],
            ['Your Lie in April', 'A-1 Pictures', 'Romance', 2014, 22, 'Finalizado', 8.6],
            ['Frieren', 'Madhouse', 'Fantasía', 2023, 28, 'En emisión', 9.2],
            ['K-On!', 'Kyoto Animation', 'Slice of life', 2009, 13, 'Finalizado', 7.8],
        ];

        foreach ($datos as [$titulo, $estudio, $genero, $anio, $eps, $estado, $calif]) {
            Anime::firstOrCreate(['titulo' => $titulo], [
                'estudio' => $estudio,
                'genero_id' => $g[$genero],
                'anio' => $anio,
                'episodios' => $eps,
                'estado' => $estado,
                'calificacion' => $calif,
                'sinopsis' => "Anime de {$genero} producido por {$estudio}.",
                'user_id' => $userId,
            ]);
        }
    }
}
