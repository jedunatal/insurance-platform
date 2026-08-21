<?php

namespace Tests;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function authenticateUser(?Tenant $tenant = null): User
    {
        $tenant ??= Tenant::firstOrCreate(
            ['id' => 1],
            [
                'name'     => 'Empresa Padrão',
                'slug'     => 'empresa-padrao',
                'email'    => 'contato@empresa.com',
                'document' => '00000000000191',
            ]
        );

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'email'     => 'test_'.uniqid().'@example.com',
        ]);

        $this->actingAs($user);

        return $user;
    }
}
