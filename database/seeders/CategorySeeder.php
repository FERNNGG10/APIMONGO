<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories =[
            ['category' =>  "Action"],
            ['category' =>  "Adventure"],
            ['category' =>  "RPG"],
            ['category' =>  "Simulation"],
            ['category' =>  "Strategy"],
            ['category' =>  "Sports"],
            ['category' =>  "Puzzle"],
            ['category' =>  "Idle"],
            ['category' =>  "Casual"],
            ['category' =>  "Arcade"],
            ['category' =>  "Racing"],
            ['category' =>  "Horror"],
            ['category' =>  "Survival"],
            ['category' =>  "Shooter"],
            ['category' =>  "Fighting"],
            ['category' =>  "Open World"],
            ['category' =>  "Sandbox"],
            ['category' =>  "Battle Royale"],
            ['category' =>  "Trivia"],
        ];

        DB::table('categories')->insert($categories);
    }
}
