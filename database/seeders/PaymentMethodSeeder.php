<?php

namespace Database\Seeders;

use App\Models\Payment_Method;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $methods = ['Credit Card', 'Debit Card', 'PayPal'];

        foreach ($methods as $method) {
            Payment_Method::create([
                'method' => $method,
                'status' => true,
            ]);
        }
    }
}
