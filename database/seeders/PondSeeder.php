<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PondType;

class PondSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PondType::create([
            'name' => 'Kilo Angkat'
        ]);
        PondType::create([
            'name' => 'Kilo Jebur'
        ]);
    }
}
