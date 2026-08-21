<?php

namespace Tests\Feature;

use App\Actions\GED\DeleteAttachmentAction;
use App\Actions\GED\StoreAttachmentAction;
use App\Actions\Quote\ConvertQuoteToPolicyAction;
use App\Actions\Quote\CreateQuoteAction;
use App\Actions\Renewal\ClonePolicyForRenewalAction;
use App\Actions\Renewal\StartPolicyRenewalAction;
use App\Actions\Renewal\UpdateRenewalStageAction;
use App\Enums\AttachmentCategoryEnum;
use App\Enums\InsuranceBranchEnum;
use App\Enums\PolicyStatusEnum;
use App\Enums\QuoteStatusEnum;
use App\Enums\RenewalLossReasonEnum;
use App\Enums\RenewalStageEnum;
use App\Livewire\Quote\ListAll as QuoteListAll;
use App\Livewire\Renewal\Pipeline as RenewalPipeline;
use App\Models\Attachment;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\PolicyRenewal;
use App\Models\Quote;
use App\Models\Tenant;
use App\Services\CRM\BrokerNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BrokerEvolutionModulesTest extends TestCase
{
    use RefreshDatabase;

    private function createTenantAndInsured(): array
    {
        $tenant = Tenant::create([
            'name'     => 'Corretora Elite',
            'slug'     => 'corretora-elite',
            'email'    => 'elite@corretora.com',
            'document' => '11222333000199',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Rodrigo Faro',
            'email'     => 'rodrigo@email.com',
            'document'  => '987.654.321-00',
        ]);

        return [$tenant, $insured];
    }

    public function test_renewal_pipeline_action_and_1_click_duplication(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();

        $policy = Policy::create([
            'tenant_id'             => $tenant->id,
            'insured_id'            => $insured->id,
            'policy_number'         => 'POL-REN-001',
            'insurer'               => 'Porto Seguro',
            'branch'                => InsuranceBranchEnum::Auto->value,
            'status'                => PolicyStatusEnum::Active->value,
            'start_date'            => now()->subYear(),
            'end_date'              => now()->addDays(15),
            'net_premium'           => 2000.00,
            'iof_amount'            => 147.60,
            'total_premium'         => 2147.60,
            'commission_percentage' => 10.00,
            'commission_amount'     => 200.00,
        ]);

        // 1. Inicia renovação
        $renewal = app(StartPolicyRenewalAction::class)->execute($policy);
        $this->assertInstanceOf(PolicyRenewal::class, $renewal);
        $this->assertEquals(RenewalStageEnum::ToContact, $renewal->stage);
        $this->assertEquals(RenewalStageEnum::ToContact, $policy->fresh()->renewal_status);

        // 2. Renova em 1-clique
        $newPolicy = app(ClonePolicyForRenewalAction::class)->execute($policy);
        $this->assertInstanceOf(Policy::class, $newPolicy);
        $this->assertNotEquals($policy->id, $newPolicy->id);
        $this->assertEquals($policy->id, $newPolicy->previous_policy_id);
        $this->assertEquals(PolicyStatusEnum::Renewed, $policy->fresh()->status);
        $this->assertEquals(RenewalStageEnum::Renewed, $renewal->fresh()->stage);
        $this->assertCount(1, $newPolicy->installments);
    }

    public function test_update_renewal_stage_and_record_loss_reason(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();

        $policy = Policy::create([
            'tenant_id'     => $tenant->id,
            'insured_id'    => $insured->id,
            'policy_number' => 'POL-LOST-001',
            'status'        => PolicyStatusEnum::Active->value,
            'end_date'      => now()->addDays(5),
            'total_premium' => 1500.00,
        ]);

        $renewal = app(StartPolicyRenewalAction::class)->execute($policy);

        app(UpdateRenewalStageAction::class)->execute(
            $renewal,
            RenewalStageEnum::Lost,
            RenewalLossReasonEnum::Price,
            'Cliente optou por seguradora concorrente mais barata'
        );

        $fresh = $renewal->fresh();
        $this->assertEquals(RenewalStageEnum::Lost, $fresh->stage);
        $this->assertEquals(RenewalLossReasonEnum::Price, $fresh->loss_reason);
        $this->assertEquals('Cliente optou por seguradora concorrente mais barata', $fresh->loss_notes);
    }

    public function test_multi_insurer_quotes_and_convert_to_policy(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();

        $quoteData = [
            'tenant_id'  => $tenant->id,
            'insured_id' => $insured->id,
            'title'      => 'Cotação Automóvel 2026',
            'branch'     => InsuranceBranchEnum::Auto->value,
        ];

        $options = [
            [
                'insurer'           => 'Porto Seguro',
                'net_premium'       => 3000.00,
                'iof_amount'        => 221.40,
                'total_premium'     => 3221.40,
                'deductible_amount' => 2500.00,
                'is_recommended'    => true,
            ],
            [
                'insurer'           => 'Allianz',
                'net_premium'       => 2800.00,
                'iof_amount'        => 206.64,
                'total_premium'     => 3006.64,
                'deductible_amount' => 2000.00,
                'is_recommended'    => false,
            ],
        ];

        $quote = app(CreateQuoteAction::class)->execute($quoteData, $options);
        $this->assertInstanceOf(Quote::class, $quote);
        $this->assertCount(2, $quote->options);

        // Converte opção recomendada em apólice
        $recommended = $quote->recommendedOption();
        $this->assertNotNull($recommended);

        $policy = app(ConvertQuoteToPolicyAction::class)->execute($quote, $recommended);
        $this->assertInstanceOf(Policy::class, $policy);
        $this->assertEquals('Porto Seguro', $policy->insurer);
        $this->assertEquals(3221.40, (float) $policy->total_premium);
        $this->assertEquals(QuoteStatusEnum::Converted, $quote->fresh()->status);
        $this->assertEquals($policy->id, $quote->fresh()->converted_policy_id);
    }

    public function test_ged_store_and_delete_attachment_action(): void
    {
        Storage::fake('public');
        [$tenant, $insured] = $this->createTenantAndInsured();

        $policy = Policy::create([
            'tenant_id'     => $tenant->id,
            'insured_id'    => $insured->id,
            'policy_number' => 'POL-GED-001',
            'status'        => PolicyStatusEnum::Active->value,
            'total_premium' => 1000.00,
        ]);

        $file = UploadedFile::fake()->create('cnh_segurado.pdf', 300, 'application/pdf');

        $attachment = app(StoreAttachmentAction::class)->execute(
            attachable: $policy,
            file: $file,
            title: 'CNH do Condutor Principal',
            category: AttachmentCategoryEnum::Cnh,
            notes: 'Documento verificado'
        );

        $this->assertInstanceOf(Attachment::class, $attachment);
        $this->assertEquals('CNH do Condutor Principal', $attachment->title);
        $this->assertEquals(AttachmentCategoryEnum::Cnh, $attachment->category);
        Storage::disk('public')->assertExists($attachment->file_path);

        // Deleta anexo
        app(DeleteAttachmentAction::class)->execute($attachment);
        $this->assertSoftDeleted('attachments', ['id' => $attachment->id]);
    }

    public function test_broker_notification_service_aggregates_urgent_alerts(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();

        // Cria apólice a vencer
        Policy::create([
            'tenant_id'     => $tenant->id,
            'insured_id'    => $insured->id,
            'policy_number' => 'POL-ALERT-01',
            'status'        => PolicyStatusEnum::Active->value,
            'end_date'      => now()->addDays(10),
            'total_premium' => 1200.00,
        ]);

        $service = app(BrokerNotificationService::class);
        $data = $service->getBrokerAlerts();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('total_unread', $data);
        $this->assertArrayHasKey('alerts', $data);
    }

    public function test_livewire_renewal_and_quote_pages_render_correctly(): void
    {
        $this->authenticateUser();

        $responseRenewals = $this->get(route('renewals.index'));
        $responseRenewals->assertStatus(200);
        $responseRenewals->assertSee('Esteira de Renovações');

        Livewire::test(RenewalPipeline::class)
            ->assertStatus(200)
            ->assertViewHas('columns');

        $responseQuotes = $this->get(route('quotes.index'));
        $responseQuotes->assertStatus(200);
        $responseQuotes->assertSee('Cotações Multi-Seguradoras');

        Livewire::test(QuoteListAll::class)
            ->assertStatus(200);
    }
}
