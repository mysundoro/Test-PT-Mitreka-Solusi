<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'Setting', 'id_permissions' => null],
            ['name' => 'List Role', 'id_permissions' => 1],
            ['name' => 'Create Role', 'id_permissions' => 1],
            ['name' => 'Edit Role', 'id_permissions' => 1],
            ['name' => 'Delete Role', 'id_permissions' => 1],
            ['name' => 'List User', 'id_permissions' => 1],
            ['name' => 'Create User', 'id_permissions' => 1],
            ['name' => 'Edit User', 'id_permissions' => 1],
            ['name' => 'Delete User', 'id_permissions' => 1],
            ['name' => 'List Menu', 'id_permissions' => 1],
            ['name' => 'Create Menu', 'id_permissions' => 1],
            ['name' => 'Edit Menu', 'id_permissions' => 1],
            ['name' => 'Delete Menu', 'id_permissions' => 1],
            ['name' => 'List Language', 'id_permissions' => 1],
            ['name' => 'Create Language', 'id_permissions' => 1],
            ['name' => 'Edit Language', 'id_permissions' => 1],
            ['name' => 'Delete Language', 'id_permissions' => 1],
            ['name' => 'Configuration', 'id_permissions' => 1],
            ['name' => 'List Permissions', 'id_permissions' => 1],
            ['name' => 'Create Permissions', 'id_permissions' => 1],
            ['name' => 'Edit Permissions', 'id_permissions' => 1],
            ['name' => 'Delete Permissions', 'id_permissions' => 1],
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'id_permissions' => $permission['id_permissions'],
            ]);
        }
    }
}
