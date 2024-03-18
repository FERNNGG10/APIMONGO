<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('consoles')->insert([
                'name' => 'Console ' . $i,
                'description' => 'Description for console ' . $i,
                'status' => true,
                'supplier_id' => $i,
            ]);
        }
    }
}
