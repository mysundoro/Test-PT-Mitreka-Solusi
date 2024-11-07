<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::create([
            'id' => 1, 'name' => 'Dashboard', 'icon' => 'mdi-view-dashboard', 'url' => 'dashboard', 'category' => 'backend'
        ]);

        // Configuration
        Menu::create([
            'id' => 2, 'name' => 'Setting', 'icon' => 'mdi-cog', 'category' => 'backend', 'id_permissions' => '1'
        ]);
        Menu::create([
            'id' => 3, 'name' => 'Configuration', 'url' => 'configuration.index', 'parent_id' => 2, 'category' => 'backend', 'id_permissions' => '18'
        ]);
        Menu::create([
            'id' => 4, 'name' => 'Language', 'url' => 'language.index', 'parent_id' => 2, 'category' => 'backend', 'id_permissions' => '14'
        ]);
        Menu::create([
            'id' => 5, 'name' => 'Permissions', 'url' => 'permissions.index', 'parent_id' => 2, 'category' => 'backend', 'id_permissions' => '19'
        ]);
        Menu::create([
            'id' => 6, 'name' => 'Roles', 'url' => 'roles.index', 'parent_id' => 2, 'category' => 'backend', 'id_permissions' => '2'
        ]);
        Menu::create([
            'id' => 7, 'name' => 'Users', 'url' => 'users.index', 'parent_id' => 2, 'category' => 'backend', 'id_permissions' => '6'
        ]);
        Menu::create([
            'id' => 8, 'name' => 'Menu', 'url' => 'menu.index', 'parent_id' => 2, 'category' => 'backend', 'id_permissions' => '10'
        ]);

        Menu::create([
            'id' => 9, 'name' => 'Audit', 'icon' => 'mdi-math-log', 'url' => 'audit.index', 'category' => 'backend'
        ]);
    }
}
