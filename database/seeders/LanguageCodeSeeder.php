<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LanguageCode;

class LanguageCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LanguageCode::create([
            'id' => 1, 'code' => 'en', 'name' => 'English'
        ]);

        LanguageCode::create([
            'id' => 2, 'code' => 'id', 'name' => 'Bahasa Indonesia'
        ]);
    }
}
