<?php

namespace Database\Seeders;

use App\Models\IndexData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndexDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IndexData::create([
            'phone' => '082291344195',
            'email' => 'kameumeutpemancingan@gmail.com',
            'address' => 'Jl. Rajawali No.F12 a, Kondangjaya, Kec. Karawang Tim., Karawang, Jawa Barat 41371',
        ]);
    }
}
