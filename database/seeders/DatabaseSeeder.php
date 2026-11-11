<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@pelianime.com'],
            ['name' => 'Administrador', 'password' => 'Admin12345', 'telefono' => '3000000000']
        );

        $this->call([
            GeneroSeeder::class,
            PeliculaSeeder::class,
            AnimeSeeder::class,
        ]);
    }
}
