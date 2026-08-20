<?php

namespace Tests\Feature;

use App\Actions\Financial\GeneratePolicyInstallmentsAction;
use App\Enums\InsuranceBranchEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PolicyStatusEnum;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyDocumentTest extends TestCase
{
    use RefreshDatabase;

    private function createSamplePolicy(): Policy
    {
        $tenant = Tenant::create([
            'name'     => 'Corretora Modelo',
            'slug'     => 'corretora-modelo',
            'email'    => 'modelo@corretora.com',
            'document' => '00000000000199',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Bernardo Soares',
            'email'     => 'bernardo@email.com',
            'document'  => '111.222.333-44',
            'phone'     => '(11) 98765-4321',
            'city'      => 'São Paulo',
            'state'     => 'SP',
        ]);

        $policy = Policy::create([
            'tenant_id'             => $tenant->id,
            'insured_id'            => $insured->id,
            'policy_number'         => 'POL-DOC-2026',
            'proposal_number'       => 'PROP-DOC-001',
            'insurer'               => 'Porto Seguro',
            'branch'                => InsuranceBranchEnum::Auto->value,
            'status'                => PolicyStatusEnum::Active->value,
            'start_date'            => now()->startOfDay(),
            'end_date'              => now()->addYear()->endOfDay(),
            'coverages'             => [
                ['name' => 'Colisão e Incêndio', 'limit_amount' => '100% FIPE', 'deductible' => 'R$ 2.500,00'],
                ['name' => 'Danos a Terceiros (RCF-V)', 'limit_amount' => 'R$ 200.000,00', 'deductible' => 'Isenta'],
            ],
            'net_premium'           => 3000.00,
            'iof_rate'              => 7.38,
            'iof_amount'            => 221.40,
            'total_premium'         => 3221.40,
            'deductible_amount'     => 2500.00,
            'commission_percentage' => 12.00,
            'commission_amount'     => 360.00,
            'payment_method'        => PaymentMethodEnum::Invoice->value,
            'installments_count'    => 3,
        ]);

        app(GeneratePolicyInstallmentsAction::class)->execute($policy);

        return $policy->fresh();
    }

    public function test_policy_document_html_view_returns_http_200_with_policy_and_insured_details(): void
    {
        $policy = $this->createSamplePolicy();

        $response = $this->get(route('policies.document.view', $policy));

        $response->assertStatus(200);
        $response->assertSee('Certificado de Apólice');
        $response->assertSee('POL-DOC-2026');
        $response->assertSee('Porto Seguro');
        $response->assertSee('Bernardo Soares');
        $response->assertSee('111.222.333-44');
        $response->assertSee('Colisão e Incêndio');
        $response->assertSee('Danos a Terceiros (RCF-V)');
        $response->assertSee('R$ 3.000,00');
        $response->assertSee('R$ 3.221,40');
        $response->assertSee('R$ 221,40');
        $response->assertSee('7,38%');
        $response->assertSee('Autenticação Digital');
    }

    public function test_policy_document_pdf_stream_returns_pdf_content_type(): void
    {
        $policy = $this->createSamplePolicy();

        $response = $this->get(route('policies.document.pdf', $policy));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type') ?? '');
    }

    public function test_policy_document_pdf_download_returns_download_response(): void
    {
        $policy = $this->createSamplePolicy();

        $response = $this->get(route('policies.document.download', $policy));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type') ?? '');
        $this->assertStringContainsString('attachment;', $response->headers->get('Content-Disposition') ?? '');
        $this->assertStringContainsString('POL-DOC-2026', $response->headers->get('Content-Disposition') ?? '');
    }
}
