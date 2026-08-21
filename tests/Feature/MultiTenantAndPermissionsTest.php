<?php

namespace Tests\Feature;

use App\Enums\PersonTypeEnum;
use App\Enums\PolicyPaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Livewire\Auth\RegisterBrokerage;
use App\Livewire\Settings\TeamManager;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class MultiTenantAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_public_guest_can_access_register_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_broker_can_self_register_and_create_isolated_tenant(): void
    {
        Livewire::test(RegisterBrokerage::class)
            ->set('brokerage_name', 'Nova Corretora Alpha')
            ->set('cnpj', '12.345.678/0001-90')
            ->set('brokerage_phone', '(11) 98888-7777')
            ->set('name', 'Roberto Corretor')
            ->set('email', 'roberto@corretoraalpha.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tenants', [
            'name'  => 'Nova Corretora Alpha',
            'email' => 'roberto@corretoraalpha.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name'  => 'Roberto Corretor',
            'email' => 'roberto@corretoraalpha.com',
        ]);

        $user = User::where('email', 'roberto@corretoraalpha.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('broker'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_multi_tenant_data_isolation_between_tenants(): void
    {
        $tenantA = Tenant::create([
            'name'     => 'Corretora A',
            'slug'     => 'corretora-a',
            'email'    => 'contato@a.com',
            'document' => '11111111000100',
        ]);

        $tenantB = Tenant::create([
            'name'     => 'Corretora B',
            'slug'     => 'corretora-b',
            'email'    => 'contato@b.com',
            'document' => '22222222000100',
        ]);

        $userA = User::create([
            'tenant_id' => $tenantA->id,
            'name'      => 'Corretor Tenant A',
            'email'     => 'user@tenant-a.com',
            'password'  => bcrypt('password'),
        ]);
        $userA->assignRole('broker');

        $userB = User::create([
            'tenant_id' => $tenantB->id,
            'name'      => 'Corretor Tenant B',
            'email'     => 'user@tenant-b.com',
            'password'  => bcrypt('password'),
        ]);
        $userB->assignRole('broker');

        // Cria Segurado no Tenant A
        $insuredA = Insured::create([
            'tenant_id'   => $tenantA->id,
            'name'        => 'Segurado do Tenant A',
            'document'    => '111.222.333-44',
            'email'       => 'segurado@a.com',
            'person_type' => PersonTypeEnum::Individual,
        ]);

        // Autentica Usuário B e tenta acessar o Segurado do Tenant A
        $this->actingAs($userB);

        // Cláusula Global Scope esconde o registro
        $this->assertNull(Insured::find($insuredA->id));

        // Rota de visualização retorna 404 (Not Found via Model Binding / Scope)
        $response = $this->get(route('insureds.view', $insuredA));
        $response->assertStatus(404);
    }

    public function test_assistant_role_is_denied_from_deleting_policies_and_insureds(): void
    {
        $tenant = Tenant::create([
            'name'     => 'Corretora Teste',
            'slug'     => 'corretora-teste',
            'email'    => 'contato@teste.com',
            'document' => '33333333000100',
        ]);

        $assistant = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Assistente Operacional',
            'email'     => 'assistente@corretora.com',
            'password'  => bcrypt('password'),
        ]);
        $assistant->assignRole('assistant');

        $insured = Insured::create([
            'tenant_id'   => $tenant->id,
            'name'        => 'Cliente Protegido',
            'document'    => '999.888.777-66',
            'email'       => 'cliente@teste.com',
            'person_type' => PersonTypeEnum::Individual,
        ]);

        $policy = Policy::create([
            'tenant_id'          => $tenant->id,
            'insured_id'         => $insured->id,
            'policy_number'      => 'APOL-PERM-001',
            'insurer'            => 'Porto Seguro',
            'branch'             => 'Auto',
            'status'             => PolicyStatusEnum::Active,
            'start_date'         => now(),
            'end_date'           => now()->addYear(),
            'net_premium'        => 2000.00,
            'iof_rate'           => 7.38,
            'iof_amount'         => 147.60,
            'total_premium'      => 2147.60,
            'payment_method'     => PolicyPaymentMethodEnum::CreditCard,
            'installments_count' => 1,
        ]);

        $this->actingAs($assistant);

        // Assistente pode visualizar
        $this->assertTrue(Gate::forUser($assistant)->allows('view', $policy));
        $this->assertTrue(Gate::forUser($assistant)->allows('view', $insured));

        // Assistente NÃO pode excluir (Policy deny)
        $this->assertFalse(Gate::forUser($assistant)->allows('delete', $policy));
        $this->assertFalse(Gate::forUser($assistant)->allows('delete', $insured));
    }

    public function test_broker_role_can_delete_and_manage_team(): void
    {
        $tenant = Tenant::create([
            'name'     => 'Corretora Master',
            'slug'     => 'corretora-master',
            'email'    => 'master@corretora.com',
            'document' => '44444444000100',
        ]);

        $broker = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Gestor Titular',
            'email'     => 'gestor@master.com',
            'password'  => bcrypt('password'),
        ]);
        $broker->assignRole('broker');

        $insured = Insured::create([
            'tenant_id'   => $tenant->id,
            'name'        => 'Cliente Excluível',
            'document'    => '555.666.777-88',
            'email'       => 'excluir@teste.com',
            'person_type' => PersonTypeEnum::Individual,
        ]);

        $policy = Policy::create([
            'tenant_id'          => $tenant->id,
            'insured_id'         => $insured->id,
            'policy_number'      => 'APOL-PERM-002',
            'insurer'            => 'Allianz',
            'branch'             => 'Auto',
            'status'             => PolicyStatusEnum::Active,
            'start_date'         => now(),
            'end_date'           => now()->addYear(),
            'net_premium'        => 1500.00,
            'iof_rate'           => 7.38,
            'iof_amount'         => 110.70,
            'total_premium'      => 1610.70,
            'payment_method'     => PolicyPaymentMethodEnum::Invoice,
            'installments_count' => 1,
        ]);

        $this->actingAs($broker);

        // Broker pode excluir
        $this->assertTrue(Gate::forUser($broker)->allows('delete', $policy));
        $this->assertTrue(Gate::forUser($broker)->allows('delete', $insured));

        // Broker pode acessar a tela de equipe
        $response = $this->get(route('settings.team'));
        $response->assertStatus(200);
    }

    public function test_assistant_role_is_forbidden_from_team_settings(): void
    {
        $tenant = Tenant::create([
            'name'     => 'Corretora Beta',
            'slug'     => 'corretora-beta',
            'email'    => 'beta@corretora.com',
            'document' => '55555555000100',
        ]);

        $assistant = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Assistente Junior',
            'email'     => 'junior@beta.com',
            'password'  => bcrypt('password'),
        ]);
        $assistant->assignRole('assistant');

        $this->actingAs($assistant);

        $response = $this->get(route('settings.team'));
        $response->assertStatus(403);
    }

    public function test_team_manager_component_can_add_and_toggle_team_members(): void
    {
        $tenant = Tenant::create([
            'name'     => 'Corretora Gamma',
            'slug'     => 'corretora-gamma',
            'email'    => 'gamma@corretora.com',
            'document' => '66666666000100',
        ]);

        $broker = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Gestor Gamma',
            'email'     => 'gestor@gamma.com',
            'password'  => bcrypt('password'),
        ]);
        $broker->assignRole('broker');

        $this->actingAs($broker);

        Livewire::test(TeamManager::class)
            ->call('openCreateModal')
            ->set('name', 'Novo Vendedor Gamma')
            ->set('email', 'vendedor@gamma.com')
            ->set('phone', '(11) 97777-6666')
            ->set('role', 'assistant')
            ->set('password', 'vendedor123')
            ->call('saveMember')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'tenant_id' => $tenant->id,
            'email'     => 'vendedor@gamma.com',
        ]);

        $newMember = User::where('email', 'vendedor@gamma.com')->first();
        $this->assertNotNull($newMember);
        $this->assertTrue($newMember->hasRole('assistant'));
        $this->assertTrue($newMember->is_active);

        // Toggle status to inactive
        Livewire::test(TeamManager::class)
            ->call('toggleUserStatus', $newMember->id);

        $this->assertFalse($newMember->fresh()->is_active);
    }
}
