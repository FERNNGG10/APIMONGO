<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            Supplier::create([
                'name' => 'Supplier ' . $i,
                'email' => 'supplier' . $i . '@example.com',
                'phone' => '1234567890',
                'status' => true,
            ]);
        }
    }
}
