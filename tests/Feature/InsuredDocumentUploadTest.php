<?php

namespace Tests\Feature;

use App\Enums\PersonTypeEnum;
use App\Livewire\Insured\Create as CreateInsured;
use App\Livewire\Quote\Create as CreateQuote;
use App\Models\Insured;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class InsuredDocumentUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        Storage::fake('private');
    }

    public function test_insured_can_be_created_with_identification_and_residence_documents_uploaded(): void
    {
        $tenant = Tenant::create([
            'name'     => 'Corretora Doc Test',
            'slug'     => 'corretora-doc-test',
            'email'    => 'doc@corretora.com',
            'document' => '12345678000100',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Corretor Upload',
            'email'     => 'upload@corretora.com',
            'password'  => bcrypt('password'),
        ]);
        $user->assignRole('broker');

        $this->actingAs($user);

        $pdfCnh = UploadedFile::fake()->create('cnh_segurado.pdf', 1024, 'application/pdf');
        $pdfComprovante = UploadedFile::fake()->create('comprovante_residencia.pdf', 800, 'application/pdf');
        $imgCpf = UploadedFile::fake()->image('cartao_cnpj.jpg', 600, 400);

        Livewire::test(CreateInsured::class)
            ->set('data.name', 'Segurado com Documentos')
            ->set('data.person_type', 'PF')
            ->set('data.document', '123.456.789-00')
            ->set('data.email', 'segurado.docs@email.com')
            ->set('data.phone', '(21) 98888-7777')
            ->set('data.zip_code', '22041-001')
            ->set('data.cnh_or_rg_path', $pdfCnh)
            ->set('data.cpf_cnpj_doc_path', $imgCpf)
            ->set('data.residence_proof_path', $pdfComprovante)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('insureds.index'));

        $this->assertDatabaseHas('insureds', [
            'name'        => 'Segurado com Documentos',
            'email'       => 'segurado.docs@email.com',
            'tenant_id'   => $tenant->id,
        ]);

        $insured = Insured::where('email', 'segurado.docs@email.com')->first();
        $this->assertNotNull($insured);
        $this->assertNotNull($insured->cnh_or_rg_path);
        $this->assertNotNull($insured->residence_proof_path);
        $this->assertNotNull($insured->cpf_cnpj_doc_path);

        // Verifica que os arquivos foram salvos no disco privado
        Storage::disk('private')->assertExists($insured->cnh_or_rg_path);
        Storage::disk('private')->assertExists($insured->residence_proof_path);
        Storage::disk('private')->assertExists($insured->cpf_cnpj_doc_path);
    }

    public function test_quote_creation_form_initializes_with_empty_options_array(): void
    {
        $tenant = Tenant::create([
            'name'     => 'Corretora Quote Clean',
            'slug'     => 'corretora-quote-clean',
            'email'    => 'quote@corretora.com',
            'document' => '98765432000100',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Corretor Quote',
            'email'     => 'broker.quote@corretora.com',
            'password'  => bcrypt('password'),
        ]);
        $user->assignRole('broker');

        $this->actingAs($user);

        // Formulário de Cotações abre limpo sem opções mockadas chumbadas
        Livewire::test(CreateQuote::class)
            ->assertSet('data.options', [])
            ->assertHasNoErrors();
    }
}
