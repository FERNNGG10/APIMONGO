<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GadgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('gadgets')->insert([
                'name' => 'Gadget ' . $i,
                'description' => 'Description for gadget ' . $i,
                'status' => true,
                'supplier_id' => $i,
            ]);
        }
    }
}
