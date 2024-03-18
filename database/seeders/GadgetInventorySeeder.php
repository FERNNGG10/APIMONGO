<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GadgetInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('gadget_inventory')->insert([
                'gadget_id' => $i,
                'stock' => rand(10, 100),
                'price' => rand(100, 500),
            ]);
        }
    }
}
