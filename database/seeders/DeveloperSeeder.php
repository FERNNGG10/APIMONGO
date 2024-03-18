<?php

namespace Database\Seeders;

use App\Models\Developer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            Developer::create([
                'name' => 'Developer ' . $i,
                'email' => 'developer' . $i . '@example.com',
                'phone' => '1234567890',
                'status' => true,
            ]);
        }
    }
}
