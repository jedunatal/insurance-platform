<?php

namespace Tests\Feature;

use App\Actions\Document\StoreDocumentAction;
use App\Enums\DocumentCategoryEnum;
use App\Models\Claim;
use App\Models\Document;
use App\Models\Insured;
use App\Models\Policy;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    private function createTenantAndInsured(): array
    {
        $tenant = Tenant::create([
            'name'     => 'Corretora Prime',
            'slug'     => 'corretora-prime',
            'email'    => 'prime@corretora.com',
            'document' => '11222333000199',
        ]);

        $insured = Insured::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Dr. Roberto Silveira',
            'email'     => 'roberto@email.com',
            'document'  => '123.456.789-00',
        ]);

        return [$tenant, $insured];
    }

    public function test_unauthenticated_users_are_redirected_when_accessing_documents(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();

        $file = UploadedFile::fake()->create('cnh_segurado.pdf', 300, 'application/pdf');
        $document = app(StoreDocumentAction::class)->execute(
            documentable: $insured,
            file: $file,
            category: DocumentCategoryEnum::Cnh,
            title: 'CNH do Titular'
        );

        $this->get(route('documents.preview', $document))->assertRedirect(route('login'));
        $this->get(route('documents.download', $document))->assertRedirect(route('login'));
        $this->delete(route('documents.destroy', $document))->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_upload_pdf_and_image_documents_to_insured_and_claim(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();
        $this->authenticateUser($tenant);

        // 1. Upload de PDF para Segurado (CNH)
        $cnhFile = UploadedFile::fake()->create('cnh_roberto.pdf', 450, 'application/pdf');
        $cnhDoc = app(StoreDocumentAction::class)->execute(
            documentable: $insured,
            file: $cnhFile,
            category: DocumentCategoryEnum::Cnh,
            title: 'CNH Digital 2026'
        );

        $this->assertDatabaseHas('documents', [
            'id'                => $cnhDoc->id,
            'tenant_id'         => $tenant->id,
            'documentable_type' => $insured->getMorphClass(),
            'documentable_id'   => $insured->id,
            'category'          => DocumentCategoryEnum::Cnh->value,
            'original_name'     => 'cnh_roberto.pdf',
        ]);

        Storage::disk('private')->assertExists($cnhDoc->file_path);

        // 2. Upload de Foto para Sinistro
        $policy = Policy::create([
            'tenant_id'     => $tenant->id,
            'insured_id'    => $insured->id,
            'policy_number' => 'POL-CLM-001',
            'status'        => 'active',
        ]);

        $claim = Claim::create([
            'tenant_id'              => $tenant->id,
            'policy_id'              => $policy->id,
            'insured_id'             => $insured->id,
            'claim_number'           => 'SIN-DOC-999',
            'status'                 => 'reported',
            'occurrence_date'        => now()->subDay(),
            'report_date'            => now(),
            'occurrence_description' => 'Colisão leve frontal',
        ]);

        $photoFile = UploadedFile::fake()->image('dano_lateral.jpg', 1200, 800);
        $photoDoc = app(StoreDocumentAction::class)->execute(
            documentable: $claim,
            file: $photoFile,
            category: DocumentCategoryEnum::DamagePhotos,
            title: 'Foto do Parachoque Danificado'
        );

        $this->assertDatabaseHas('documents', [
            'id'                => $photoDoc->id,
            'tenant_id'         => $tenant->id,
            'documentable_type' => $claim->getMorphClass(),
            'documentable_id'   => $claim->id,
            'category'          => DocumentCategoryEnum::DamagePhotos->value,
            'original_name'     => 'dano_lateral.jpg',
        ]);

        Storage::disk('private')->assertExists($photoDoc->file_path);
    }

    public function test_upload_validation_blocks_disallowed_mime_types_and_executable_files(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();
        $this->authenticateUser($tenant);

        $maliciousFile = UploadedFile::fake()->create('virus.exe', 100, 'application/x-msdownload');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('não é permitido por motivos de segurança');

        app(StoreDocumentAction::class)->execute(
            documentable: $insured,
            file: $maliciousFile,
            category: DocumentCategoryEnum::Other,
            title: 'Arquivo Executável Suspeito'
        );
    }

    public function test_secure_download_retrieves_file_from_private_storage(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();
        $this->authenticateUser($tenant);

        $pdfFile = UploadedFile::fake()->create('comprovante_residencia.pdf', 320, 'application/pdf');
        $document = app(StoreDocumentAction::class)->execute(
            documentable: $insured,
            file: $pdfFile,
            category: DocumentCategoryEnum::ProofOfResidence,
            title: 'Comprovante de Luz'
        );

        $response = $this->get(route('documents.download', $document));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=comprovante_residencia.pdf');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_secure_preview_streams_file_inline_from_private_storage(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();
        $this->authenticateUser($tenant);

        $pdfFile = UploadedFile::fake()->create('apolice_digital.pdf', 500, 'application/pdf');
        $document = app(StoreDocumentAction::class)->execute(
            documentable: $insured,
            file: $pdfFile,
            category: DocumentCategoryEnum::PolicyDocument,
            title: 'Apólice Emitida'
        );

        $response = $this->get(route('documents.preview', $document));

        $response->assertStatus(200);
        $this->assertStringContainsString('inline; filename="apolice_digital.pdf"', $response->headers->get('Content-Disposition') ?? '');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_multi_tenant_isolation_prevents_user_from_tenant_b_accessing_tenant_a_documents(): void
    {
        // Tenant A cria um documento confidencial
        [$tenantA, $insuredA] = $this->createTenantAndInsured();
        $pdfFile = UploadedFile::fake()->create('rg_confidencial.pdf', 200, 'application/pdf');
        $docA = app(StoreDocumentAction::class)->execute(
            documentable: $insuredA,
            file: $pdfFile,
            category: DocumentCategoryEnum::Rg,
            title: 'RG Titular Confidencial'
        );

        // Tenant B é criado
        $tenantB = Tenant::create([
            'name'     => 'Outra Corretora Concorrente',
            'slug'     => 'outra-corretora',
            'email'    => 'concorrente@email.com',
            'document' => '99888777000100',
        ]);

        // Autentica como usuário do Tenant B
        $this->authenticateUser($tenantB);

        // Usuário do Tenant B tenta baixar o documento do Tenant A -> Bloqueado por 404 (Global Scope)
        $this->get(route('documents.download', $docA))->assertStatus(404);
        $this->get(route('documents.preview', $docA))->assertStatus(404);
    }

    public function test_destroy_removes_physical_file_from_private_disk_and_database(): void
    {
        [$tenant, $insured] = $this->createTenantAndInsured();
        $this->authenticateUser($tenant);

        $pdfFile = UploadedFile::fake()->create('documento_para_deletar.pdf', 150, 'application/pdf');
        $document = app(StoreDocumentAction::class)->execute(
            documentable: $insured,
            file: $pdfFile,
            category: DocumentCategoryEnum::Other,
            title: 'Documento Temporário'
        );

        Storage::disk('private')->assertExists($document->file_path);

        $response = $this->delete(route('documents.destroy', $document));
        $response->assertRedirect();

        Storage::disk('private')->assertMissing($document->file_path);
        $this->assertSoftDeleted('documents', [
            'id' => $document->id,
        ]);
    }
}
