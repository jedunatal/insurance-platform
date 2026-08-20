<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table): void {
            $table->id();

            // --- Tenant & Relacionamentos ---
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('insured_id')->constrained('insureds')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('broker_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            // --- Dados do Contrato ---
            $table->string('policy_number')->unique();
            $table->string('proposal_number')->nullable();
            $table->string('insurer')->nullable()->comment('Seguradora (ex: Porto Seguro, Bradesco, Allianz, Tokio Marine, SulAmérica)');
            $table->string('branch')->nullable()->comment('Ramo do seguro (ex: Automóvel, Vida, Residencial, Empresarial)');
            $table->string('branch_code', 6)->nullable()->comment('Código do ramo SUSEP (ex: 171 - Equipamentos)');
            $table->string('susep_process')->nullable()->comment('Número do processo SUSEP');
            $table->string('ci_code')->nullable()->comment('Código do CI (Comunicado Interno)');
            $table->string('status')->default('active')->index();

            // --- Vigência ---
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();

            // --- Objeto Segurado (JSON) ---
            $table->json('insured_object')->nullable();

            // --- Coberturas & Franquias (JSON) ---
            $table->json('coverages')->nullable();

            // --- Financeiro & Pagamento ---
            $table->decimal('net_premium', 14, 2)->default(0);
            $table->decimal('iof_amount', 14, 2)->default(0);
            $table->decimal('total_premium', 14, 2)->default(0);
            $table->decimal('deductible_amount', 14, 2)->default(0)->comment('Valor da Franquia');
            $table->string('payment_method')->default('invoice');
            $table->unsignedSmallInteger('installments_count')->default(1);
            $table->json('installments_schedule')->nullable()->comment('Tabela de parcelas (vencimento, valor, status)');

            // --- Metadados ---
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices auxiliares para listagem multi-tenant
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'insured_id']);
            $table->index(['tenant_id', 'broker_id']);
            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
