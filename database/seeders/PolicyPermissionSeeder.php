<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Cria o conjunto de permissões do módulo de Apólices.
 *
 * Permissões: view, create, update, delete policies.
 * Criado idempotente para poder rodar várias vezes sem duplicar registros.
 */
class PolicyPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');

        $permissions = [
            'view policies',
            'create policies',
            'update policies',
            'delete policies',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => $guard],
                ['name' => $name, 'guard_name' => $guard]
            );
        }

        $super = Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => $guard],
            ['name' => 'super-admin', 'guard_name' => $guard]
        );

        $broker = Role::firstOrCreate(
            ['name' => 'broker', 'guard_name' => $guard],
            ['name' => 'broker', 'guard_name' => $guard]
        );

        $super->givePermissionTo($permissions);
        $broker->syncPermissions(['view policies', 'create policies', 'update policies']);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
