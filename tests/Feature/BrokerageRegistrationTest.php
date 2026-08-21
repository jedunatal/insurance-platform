<?php

namespace Tests\Feature;

use App\Livewire\Auth\RegisterBrokerage;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BrokerageRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_broker_registration_with_masked_cnpj_and_phone_sanitizes_and_saves_clean_data(): void
    {
        Livewire::test(RegisterBrokerage::class)
            ->set('brokerage_name', '  Prime Seguros & Benefícios  ')
            ->set('cnpj', '12.345.678/0001-95')
            ->set('brokerage_phone', '(11) 98765-4321')
            ->set('name', '  Carlos Eduardo Silveira  ')
            ->set('email', '  CARLOS@PrimeSeguros.com.br  ')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        // Verifica que o CNPJ foi higienizado (somente números) no banco de dados
        $this->assertDatabaseHas('tenants', [
            'name'     => 'Prime Seguros & Benefícios',
            'document' => '12345678000195',
            'email'    => 'carlos@primeseguros.com.br',
        ]);

        // Verifica que o e-mail do usuário foi sanitizado em minúsculas
        $this->assertDatabaseHas('users', [
            'name'  => 'Carlos Eduardo Silveira',
            'email' => 'carlos@primeseguros.com.br',
        ]);

        $user = User::where('email', 'carlos@primeseguros.com.br')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('broker'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_fails_when_cnpj_has_invalid_length(): void
    {
        Livewire::test(RegisterBrokerage::class)
            ->set('brokerage_name', 'Corretora Inválida')
            ->set('cnpj', '12.345.678/000') // Apenas 11 dígitos
            ->set('brokerage_phone', '(11) 98765-4321')
            ->set('name', 'Roberto')
            ->set('email', 'roberto@invalida.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('register')
            ->assertHasErrors(['cnpj']);
    }

    public function test_registration_fails_when_phone_is_too_short(): void
    {
        Livewire::test(RegisterBrokerage::class)
            ->set('brokerage_name', 'Corretora Inválida')
            ->set('cnpj', '12.345.678/0001-95')
            ->set('brokerage_phone', '(11) 999') // Muito curto
            ->set('name', 'Roberto')
            ->set('email', 'roberto@invalida.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('register')
            ->assertHasErrors(['brokerage_phone']);
    }

    public function test_registration_fails_when_email_already_exists(): void
    {
        $tenant = Tenant::create([
            'name'     => 'Corretora Existente',
            'slug'     => 'existente',
            'email'    => 'contato@existente.com',
            'document' => '99999999000100',
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Usuario Existente',
            'email'     => 'duplicado@corretora.com',
            'password'  => bcrypt('password'),
        ]);

        Livewire::test(RegisterBrokerage::class)
            ->set('brokerage_name', 'Outra Corretora')
            ->set('cnpj', '88.888.888/0001-88')
            ->set('brokerage_phone', '(11) 98888-7777')
            ->set('name', 'Novo Usuario')
            ->set('email', 'duplicado@corretora.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    public function test_registration_fails_when_cnpj_already_registered_in_another_tenant(): void
    {
        Tenant::create([
            'name'     => 'Corretora Original',
            'slug'     => 'original',
            'email'    => 'original@corretora.com',
            'document' => '12345678000195',
        ]);

        Livewire::test(RegisterBrokerage::class)
            ->set('brokerage_name', 'Corretora Clone')
            ->set('cnpj', '12.345.678/0001-95')
            ->set('brokerage_phone', '(11) 98888-7777')
            ->set('name', 'Clone User')
            ->set('email', 'clone@corretora.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('register')
            ->assertHasErrors(['cnpj']);
    }
}
