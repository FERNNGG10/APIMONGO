<?php

namespace Database\Seeders;

use App\Models\Classification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $classifications = ['E', 'E10+', 'T', 'M', 'AO', 'RP', 'EC', 'KA', 'G', 'PG'];

        foreach ($classifications as $classification) {
            Classification::create([
                'classification' => $classification,
                'status' => true,
            ]);
        }
    }
}
