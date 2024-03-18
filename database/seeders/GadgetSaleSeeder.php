<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GadgetSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('gadget_sales')->insert([
                'gadget_id' => $i,
                'user_id' => 1,
                'payment_method_id' => 3,
                'quantity' => rand(1, 10),
                'total' => rand(100, 500),
            ]);
        }
    }
}
