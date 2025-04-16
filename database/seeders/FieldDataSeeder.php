<?php

namespace Database\Seeders;

use App\Models\FieldData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FieldDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FieldData::create([
            'name' => 'A1',
            'description' => 'Ini deskripsi lapak A1',
            'field_type' => 'Kilo Angkat',
            'field_material' => 'Mas',
            'field_location' => '1 kg',
            'morning_price' => 10000,
            'night_price' => 15000,
            'thumbnail' => 'no_img.jpg',
        ]);
        FieldData::create([
            'name' => 'A2',
            'description' => 'Ini deskripsi lapak A2',
            'field_type' => 'Kilo Angkat',
            'field_material' => 'Mas',
            'field_location' => '1 kg',
            'morning_price' => 10000,
            'night_price' => 15000,
            'thumbnail' => 'no_img.jpg',
        ]);




        FieldData::create([
            'name' => 'J1',
            'description' => 'Ini deskripsi lapak J1',
            'field_type' => 'Kilo Jebur',
            'field_material' => 'Jambal',
            'field_location' => '1 kg',
            'morning_price' => 15000,
            'night_price' => 20000,
            'thumbnail' => 'no_img.jpg',
        ]);
        FieldData::create([
            'name' => 'J2',
            'description' => 'Ini deskripsi lapak J2',
            'field_type' => 'Kilo Jebur',
            'field_material' => 'Jambal',
            'field_location' => '1 kg',
            'morning_price' => 15000,
            'night_price' => 20000,
            'thumbnail' => 'no_img.jpg',
        ]);
    }
}