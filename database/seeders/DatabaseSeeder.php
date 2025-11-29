<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CatalogosSeeder::class,
            UsersSeeder::class,
            PrediosSeeder::class,
            ZonasSeeder::class,
            TareasPorZonaSeeder::class,
            ActivosSeeder::class,
            ActividadesSeeder::class,
        ]);
    }
}
