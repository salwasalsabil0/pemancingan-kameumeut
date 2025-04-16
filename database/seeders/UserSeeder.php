<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Salwa Salsabila',
            'username' => 'salwasalsabila',
            'email' => 'salwa.ss906@gmail.com',
            'password' => bcrypt('123'),
            'role_id' => 1,
            'phone' => '081234567890',
        ]);


        User::create([
            'name' => 'Salsa',
            'username' => 'sals10',
            'email' => 'awasalsabila10@gmail.com',
            'password' => bcrypt('123'),
            'role_id' => 2,
            'phone' => '081234567895',
        ]);

    }
}