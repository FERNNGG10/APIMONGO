<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['name'=>"Fernando",'email'=>"fgolmos10@gmail.com",'password'=>Hash::make('12345678'),'rol_id'=>1,'code'=>Crypt::encrypt(rand(100000,999999)),'status'=>true]
        ];

        DB::table('users')->insert($users);
    }
}
