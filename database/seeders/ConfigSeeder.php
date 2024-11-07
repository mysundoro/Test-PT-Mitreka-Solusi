<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Configuration;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            ['key' => 'logo_system', 'value' => '', 'description' => null],
            ['key' => 'login_logo', 'value' => '', 'description' => null],
            ['key' => 'favicon', 'value' => '', 'description' => null],
            ['key' => 'application_name', 'value' => 'Tes Modul', 'description' => null],
            ['key' => 'timezone', 'value' => 'Asia/Jakarta', 'description' => null],
        ];

        foreach ($configurations as $config) {
            Configuration::updateOrCreate(
                ['key' => $config['key']],
                ['value' => $config['value'], 'description' => $config['description']]
            );
        }
    }
}
