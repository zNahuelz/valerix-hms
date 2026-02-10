<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            'name' => 'Permisos administrativos.',
            'key' => 'sys:admin'
        ]);
    }
}
