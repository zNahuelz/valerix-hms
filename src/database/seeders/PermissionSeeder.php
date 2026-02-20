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
        if (Permission::exists()) {
            return;
        }

        Permission::create([
            'name' => 'sys:admin',
        ]);
    }
}
