<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsoleSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('console_sales')->insert([
                'console_id' => $i,
                'user_id' => 1,
                'payment_method_id' => 2,
                'quantity' => rand(1, 10),
                'total' => rand(100, 500),
            ]);
        }
    }
}
