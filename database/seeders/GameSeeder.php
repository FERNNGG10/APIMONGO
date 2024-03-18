<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            Game::create([
                'name' => 'Game ' . $i,
                'description' => 'This is game ' . $i,
                'status' => true,
                'category_id' => 1, // Asume que 1 es una categoría válida en tu base de datos
                'classification_id' => 1, // Asume que 1 es una clasificación válida en tu base de datos
                'developer_id' => 1, // Asume que 1 es un desarrollador válido en tu base de datos
                'supplier_id' => 1, // Asume que 1 es un proveedor válido en tu base de datos
            ]);
        }
    }
}
