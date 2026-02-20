<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Role::exists()) {
            return;
        }

        if (! Permission::exists()) {
            return;
        }

        $adminRole = Role::create(
            ['name' => 'ADMINISTRADOR']
        );

        $permission = Permission::findByName('sys:admin');
        $adminRole->givePermissionTo($permission);

        Role::create(['name' => 'GERENTE']);
        Role::create(['name' => 'DOCTOR']);
        Role::create(['name' => 'ENFERMERA']);
        Role::create(['name' => 'SECRETARIA']);
    }
}
