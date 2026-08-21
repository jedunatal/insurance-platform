<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            
            // Relacionamento Polimórfico (Insured, Policy, Claim, etc.)
            $table->string('documentable_type');
            $table->unsignedBigInteger('documentable_id');
            
            // Categoria do Documento (CNH, RG, Comprovante de Residência, Fotos de Sinistro, etc.)
            $table->string('category', 50)->default('other');
            $table->string('title')->nullable();
            
            // Metadados do Arquivo
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size')->default(0);
            
            // Auditoria e Rastreamento
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices de Alta Performance
            $table->index(['tenant_id', 'documentable_type', 'documentable_id'], 'documents_tenant_morph_idx');
            $table->index(['tenant_id', 'category'], 'documents_tenant_category_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
