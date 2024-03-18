<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Game_Inventory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $games = Game::all();

        foreach ($games as $game) {
            Game_Inventory::create([
                'game_id' => $game->id,
                'stock' => 100,
                'price' => 59.99,
            ]);
        }
    }
}
