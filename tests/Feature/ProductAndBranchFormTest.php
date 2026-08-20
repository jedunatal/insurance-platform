<?php

namespace Tests\Feature;

use App\Enums\InsuranceBranchEnum;
use App\Livewire\Lead\Create as LeadCreate;
use App\Livewire\Policy\Create as PolicyCreate;
use App\Models\Product;
use App\Models\Tenant;
use App\Services\Insurance\PolicyService;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductAndBranchFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_insurance_branch_enum_options_returns_valid_labels(): void
    {
        $options = InsuranceBranchEnum::options();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('Automóvel', $options);
        $this->assertArrayHasKey('Vida', $options);
        $this->assertArrayHasKey('Residencial', $options);
        $this->assertArrayHasKey('Empresarial', $options);
        $this->assertEquals('Automóvel', $options['Automóvel']);
        $this->assertEquals('Vida', $options['Vida']);
    }

    public function test_product_seeder_populates_catalog_for_all_branches(): void
    {
        $this->seed(ProductSeeder::class);

        $this->assertDatabaseHas('tenants', ['id' => 1]);
        $this->assertTrue(Product::count() >= 20);

        // Verifica produtos por ramo
        $autoProducts = Product::where('branch', InsuranceBranchEnum::Auto->value)->get();
        $this->assertNotEmpty($autoProducts);

        $lifeProducts = Product::where('branch', InsuranceBranchEnum::Life->value)->get();
        $this->assertNotEmpty($lifeProducts);

        $homeProducts = Product::where('branch', InsuranceBranchEnum::Home->value)->get();
        $this->assertNotEmpty($homeProducts);
    }

    public function test_policy_service_filters_products_by_branch(): void
    {
        $this->seed(ProductSeeder::class);
        $service = app(PolicyService::class);

        $allProducts = $service->productOptions(1);
        $this->assertNotEmpty($allProducts);

        $autoProducts = $service->productOptions(1, InsuranceBranchEnum::Auto->value);
        $this->assertNotEmpty($autoProducts);

        $lifeProducts = $service->productOptions(1, InsuranceBranchEnum::Life->value);
        $this->assertNotEmpty($lifeProducts);

        // Garante que o filtro é restrito ao ramo selecionado
        foreach ($autoProducts as $id => $name) {
            $product = Product::find($id);
            $this->assertEquals(InsuranceBranchEnum::Auto->value, $product->branch);
        }
    }

    public function test_lead_and_policy_forms_render_successfully(): void
    {
        $this->seed(ProductSeeder::class);

        Livewire::test(LeadCreate::class)
            ->assertStatus(200)
            ->assertSee('Ramo / Produto de Interesse')
            ->assertSee('Origem do Cliente');

        Livewire::test(PolicyCreate::class)
            ->assertStatus(200)
            ->assertSee('Ramo do Seguro')
            ->assertSee('Produto / Catálogo');
    }
}
