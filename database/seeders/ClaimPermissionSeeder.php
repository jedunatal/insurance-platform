<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ClaimPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view claims',
            'create claims',
            'update claims',
            'delete claims',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}