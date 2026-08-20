<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('insured_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('claim_number')->nullable()->index(); // Nº interno do Sinistro
            $table->string('protocol_number')->nullable()->index(); // Protocolo / Nº Sinistro Seguradora
            $table->string('insurer_claim_number')->nullable(); // Nº do Sinistro na Seguradora (alias/compat)
            $table->string('claim_type')->nullable()->comment('Tipo: Colisão, Roubo/Furto, Terceiros, Incêndio, etc.');
            $table->string('status')->default('reported');

            $table->dateTime('occurrence_date');
            $table->dateTime('report_date');

            $table->decimal('estimated_amount', 12, 2)->default(0);
            $table->decimal('indemnified_amount', 12, 2)->default(0);
            $table->decimal('deductible_amount', 12, 2)->default(0); // Valor da Franquia

            $table->text('occurrence_description');
            $table->string('location')->nullable();
            $table->json('third_party_details')->nullable(); // Terceiros envolvidos
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status', 'occurrence_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};