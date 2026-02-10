<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        $permission = Permission::where('_key', 'sys:admin')->first();
        if (!$permission) {
            return;
        }

        $adminRole = Role::firstOrCreate(
            ['name' => 'ADMINISTRADOR']
        );
        $adminRole->permissions()->syncWithoutDetaching([$permission->id]);

        Role::create(['name' => 'GERENTE']);
        Role::create(['name' => 'DOCTOR']);
        Role::create(['name' => 'ENFERMERA']);
        Role::create(['name' => 'SECRETARIA']);
    }
}
