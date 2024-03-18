<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Game_Sale;
use App\Models\Payment_Method;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();
        $game = Game::first();
        $paymentMethod = Payment_Method::first();

        Game_Sale::create([
            'game_id' => $game->id,
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethod->id,
            'quantity' => 1,
            'total' => 59.99,
        ]);
    }
}
