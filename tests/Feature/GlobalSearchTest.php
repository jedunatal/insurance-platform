<?php

namespace Tests\Feature;

use App\Enums\ClaimStatusEnum;
use App\Enums\ClaimTypeEnum;
use App\Enums\InsuranceBranchEnum;
use App\Enums\LeadStatusEnum;
use App\Enums\PolicyStatusEnum;
use App\Livewire\Layout\GlobalSearch;
use App\Models\Claim;
use App\Models\Insured;
use App\Models\Lead;
use App\Models\Policy;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_search_returns_empty_collections_when_query_too_short_or_empty(): void
    {
        $component = new GlobalSearch();

        $component->query = '';
        $results = $component->getSearchResults();
        $this->assertEquals(0, $results['totalCount']);
        $this->assertTrue($results['leads']->isEmpty());

        $component->query = 'a';
        $results = $component->getSearchResults();
        $this->assertEquals(0, $results['totalCount']);

        $component->query = '   ';
        $results = $component->getSearchResults();
        $this->assertEquals(0, $results['totalCount']);
    }

    public function test_global_search_handles_special_characters_without_exceptions(): void
    {
        $component = new GlobalSearch();
        $component->query = '!@#$%^&*()_+=[]{}|;:,.<>?';

        $results = $component->getSearchResults();
        $this->assertIsArray($results);
        $this->assertEquals(0, $results['totalCount']);
    }

    public function test_global_search_finds_insured_by_name_and_document(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Search',
            'slug' => 'corretora-search',
            'email' => 'search@corretora.com',
            'document' => '00000000000191',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Ricardo de Oliveira',
            'email' => 'ricardo@email.com',
            'document' => '123.456.789-00',
            'city' => 'Curitiba',
            'state' => 'PR',
        ]);

        // Busca por nome
        $component = new GlobalSearch();
        $component->query = 'Ricardo';
        $results = $component->getSearchResults();

        $this->assertTrue($results['insureds']->contains('id', $insured->id));
        $this->assertEquals('Ricardo de Oliveira', $results['insureds']->first()->name);

        // Busca por documento limpo sem pontuação
        $component->query = '12345678900';
        $resultsDoc = $component->getSearchResults();
        $this->assertTrue($resultsDoc['insureds']->contains('id', $insured->id));
    }

    public function test_global_search_finds_policy_by_number_and_insurer(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Search 2',
            'slug' => 'corretora-search-2',
            'email' => 'search2@corretora.com',
            'document' => '00000000000192',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Juliana Paes',
            'email' => 'juliana@email.com',
        ]);

        $policy = Policy::create([
            'tenant_id' => $tenant->id,
            'insured_id' => $insured->id,
            'policy_number' => 'POL-SEARCH-999',
            'insurer' => 'Tokio Marine',
            'branch' => InsuranceBranchEnum::Auto->value,
            'status' => PolicyStatusEnum::Active->value,
            'total_premium' => 2500.00,
        ]);

        $component = new GlobalSearch();
        $component->query = 'POL-SEARCH';
        $results = $component->getSearchResults();

        $this->assertTrue($results['policies']->contains('id', $policy->id));
        $this->assertEquals('POL-SEARCH-999', $results['policies']->first()->policy_number);
    }

    public function test_global_search_finds_claim_by_protocol_and_type(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Search 3',
            'slug' => 'corretora-search-3',
            'email' => 'search3@corretora.com',
            'document' => '00000000000193',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Carlos Eduardo',
            'email' => 'carlos@email.com',
        ]);

        $policy = Policy::create([
            'tenant_id' => $tenant->id,
            'insured_id' => $insured->id,
            'policy_number' => 'POL-CLAIM-123',
            'insurer' => 'Porto Seguro',
            'branch' => InsuranceBranchEnum::Auto->value,
            'status' => PolicyStatusEnum::Active->value,
            'total_premium' => 1500.00,
        ]);

        $claim = Claim::create([
            'tenant_id' => $tenant->id,
            'policy_id' => $policy->id,
            'insured_id' => $insured->id,
            'claim_number' => 'SIN-GLOBAL-01',
            'protocol_number' => 'PROT-GLOBAL-777',
            'claim_type' => ClaimTypeEnum::Collision->value,
            'status' => ClaimStatusEnum::Reported->value,
            'occurrence_date' => now(),
            'report_date' => now(),
            'occurrence_description' => 'Colisão frontal',
        ]);

        $component = new GlobalSearch();
        $component->query = 'PROT-GLOBAL';
        $results = $component->getSearchResults();

        $this->assertTrue($results['claims']->contains('id', $claim->id));
        $this->assertEquals('PROT-GLOBAL-777', $results['claims']->first()->protocol_number);
    }

    public function test_global_search_finds_lead_by_name(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Search 4',
            'slug' => 'corretora-search-4',
            'email' => 'search4@corretora.com',
            'document' => '00000000000194',
        ]);

        $lead = Lead::create([
            'tenant_id' => $tenant->id,
            'name' => 'Camila Rocha',
            'email' => 'camila@email.com',
            'phone' => '11988887777',
            'status' => LeadStatusEnum::New->value,
        ]);

        $component = new GlobalSearch();
        $component->query = 'Camila';
        $results = $component->getSearchResults();

        $this->assertTrue($results['leads']->contains('id', $lead->id));
        $this->assertEquals('Camila Rocha', $results['leads']->first()->name);
    }

    public function test_global_search_livewire_component_interactivity_and_rendering(): void
    {
        $tenant = Tenant::create([
            'name' => 'Corretora Livewire',
            'slug' => 'corretora-livewire',
            'email' => 'livewire@corretora.com',
            'document' => '00000000000195',
        ]);

        Insured::create([
            'tenant_id' => $tenant->id,
            'name' => 'Valdirene Aparecida',
            'email' => 'valdirene@email.com',
            'document' => '999.888.777-66',
        ]);

        Livewire::test(GlobalSearch::class)
            ->set('query', 'Valdirene')
            ->assertSet('isOpen', true)
            ->assertSee('Valdirene Aparecida')
            ->assertSee('Segurados Cadastrados')
            ->call('clearSearch')
            ->assertSet('query', '')
            ->assertSet('isOpen', false);
    }
}
