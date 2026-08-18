<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpar cache de permissões do Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Matriz de Permissões por Módulo
        $modules = [
            'leads' => ['view', 'create', 'update', 'delete'],
            'insureds' => ['view', 'create', 'update', 'delete'],
            'products' => ['view', 'create', 'update', 'delete'],
            'policies' => ['view', 'create', 'update', 'delete'],
            'claims' => ['view', 'create', 'update', 'delete'],
        ];

        $allPermissions = [];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = "{$action} {$module}";
                $allPermissions[] = $permissionName;
                Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            }
        }

        // 2. Criação dos Cargos Padrão (Roles)

        // Cargo: Super Administrador (Acesso total)
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Cargo: Corretor / Gestor da Corretora (CRUD completo operacional)
        $broker = Role::firstOrCreate(['name' => 'broker', 'guard_name' => 'web']);
        $broker->syncPermissions($allPermissions);

        // Cargo: Consultor / Vendedor (Não exclui registros sensíveis e não altera produtos)
        $consultant = Role::firstOrCreate(['name' => 'consultant', 'guard_name' => 'web']);
        $consultant->syncPermissions([
            'view leads', 'create leads', 'update leads',
            'view insureds', 'create insureds', 'update insureds',
            'view products',
            'view policies', 'create policies', 'update policies',
            'view claims', 'create claims', 'update claims',
        ]);
    }
}