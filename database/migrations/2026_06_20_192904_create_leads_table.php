<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table): void {
            $table->id();

            // Multi-tenancy
            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            // Ramo / Produto de Interesse (Novo)
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            // Atribuição de Responsável e Criador
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // CORREÇÃO: Nullable para permitir cadastros automáticos vindos da API/Site
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Dados do Cliente em Potencial
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('phone', 20)->nullable()->index();
            $table->string('document', 20)->nullable(); // CPF ou CNPJ (Opcional, útil para cotação)

            // Controle da Origem e Status
            $table->string('source', 30)->default('manual'); // Ex: 'site', 'manual', 'whatsapp', 'indicacao'
            $table->string('status', 30)->default('novo');    // Ex: 'novo', 'em_atendimento', 'cotado', 'ganho', 'perdido'

            // Agenda e Acompanhamento
            $table->dateTime('next_contact_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices de Alta Performance
            $table->index(
                ['tenant_id', 'status'],
                'leads_tenant_status_idx'
            );

            $table->index(
                ['tenant_id', 'product_id'],
                'leads_tenant_product_idx'
            );

            $table->index(
                ['tenant_id', 'assigned_to'],
                'leads_tenant_consultant_idx'
            );

            $table->index(
                ['tenant_id', 'created_at'],
                'leads_tenant_created_idx'
            );

            $table->index(
                ['tenant_id', 'next_contact_at'],
                'leads_tenant_next_contact_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};