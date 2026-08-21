<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insureds', function (Blueprint $table): void {
            if (! Schema::hasColumn('insureds', 'cnh_or_rg_path')) {
                $table->string('cnh_or_rg_path')->nullable()->after('notes');
            }
            if (! Schema::hasColumn('insureds', 'cpf_cnpj_doc_path')) {
                $table->string('cpf_cnpj_doc_path')->nullable()->after('cnh_or_rg_path');
            }
            if (! Schema::hasColumn('insureds', 'residence_proof_path')) {
                $table->string('residence_proof_path')->nullable()->after('cpf_cnpj_doc_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('insureds', function (Blueprint $table): void {
            $table->dropColumn([
                'cnh_or_rg_path',
                'cpf_cnpj_doc_path',
                'residence_proof_path',
            ]);
        });
    }
};
