<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Garante a existência do Tenant Principal
        $tenant = Tenant::firstOrCreate(
            ['id' => 1],
            [
                'name'     => 'Corretora de Seguros Padrão',
                'slug'     => 'corretora-padrao',
                'email'    => 'contato@corretora.com',
                'document' => '00000000000191',
                'phone'    => '(11) 3000-0000',
            ]
        );

        // 2. Administrador Geral (Super Admin)
        $admin = User::firstOrCreate(
            ['email' => 'admin@seguradora.com'],
            [
                'tenant_id' => $tenant->id,
                'name'      => 'Administrador da Corretora',
                'password'  => Hash::make('password'),
            ]
        );

        // Atualiza a senha caso o usuário já existisse
        $admin->update([
            'password' => Hash::make('password'),
        ]);

        if (Role::where('name', 'super-admin')->exists()) {
            $admin->syncRoles(['super-admin']);
        }

        // 3. Usuário Corretor / Gestor Comercial
        $broker = User::firstOrCreate(
            ['email' => 'corretor@corretora.com'],
            [
                'tenant_id' => $tenant->id,
                'name'      => 'Gestor (Gestor Principal)',
                'password'  => Hash::make('password'),
            ]
        );

        $broker->update([
            'password' => Hash::make('password'),
        ]);

        if (Role::where('name', 'broker')->exists()) {
            $broker->syncRoles(['broker']);
        }
    }
}
