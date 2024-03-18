<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Gadget;
use App\Models\Game_Sale;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            RolSeeder::class,
            CategorySeeder::class,
            ClassificationSeeder::class,
            DeveloperSeeder::class,
            SupplierSeeder::class,
            PaymentMethodSeeder::class,
            GameSeeder::class,
            GameInventorySeeder::class,
            ConsoleSeeder::class,
            ConsoleInventorySeeder::class,
            GadgetSeeder::class,
            GadgetInventorySeeder::class,
            UserSeeder::class,
            GameSaleSeeder::class,
            ConsoleSaleSeeder::class,
            GadgetSaleSeeder::class,
            ReviewSeeder::class,
            DlcSeeder::class
           
        ]);

    }
}
