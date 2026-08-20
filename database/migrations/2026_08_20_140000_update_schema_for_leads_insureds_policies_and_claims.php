<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Atualizações na tabela 'insureds'
        if (Schema::hasTable('insureds')) {
            Schema::table('insureds', function (Blueprint $table): void {
                if (! Schema::hasColumn('insureds', 'birth_date')) {
                    $table->date('birth_date')->nullable()->after('person_type');
                }
                if (! Schema::hasColumn('insureds', 'lead_id')) {
                    $table->foreignId('lead_id')->nullable()->after('tenant_id')->constrained('leads')->nullOnDelete();
                }
            });
        }

        // 2. Atualizações na tabela 'policies'
        if (Schema::hasTable('policies')) {
            Schema::table('policies', function (Blueprint $table): void {
                if (! Schema::hasColumn('policies', 'insurer')) {
                    $table->string('insurer')->nullable()->after('proposal_number');
                }
                if (! Schema::hasColumn('policies', 'branch')) {
                    $table->string('branch')->nullable()->after('insurer');
                }
                if (! Schema::hasColumn('policies', 'deductible_amount')) {
                    $table->decimal('deductible_amount', 14, 2)->default(0)->after('total_premium');
                }
            });
        }

        // 3. Atualizações na tabela 'claims'
        if (Schema::hasTable('claims')) {
            Schema::table('claims', function (Blueprint $table): void {
                if (! Schema::hasColumn('claims', 'protocol_number')) {
                    $table->string('protocol_number')->nullable()->after('claim_number');
                }
                if (! Schema::hasColumn('claims', 'claim_type')) {
                    $table->string('claim_type')->nullable()->after('insurer_claim_number');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('policies')) {
            Schema::table('policies', function (Blueprint $table): void {
                if (Schema::hasColumn('policies', 'insurer')) {
                    $table->dropColumn('insurer');
                }
                if (Schema::hasColumn('policies', 'branch')) {
                    $table->dropColumn('branch');
                }
                if (Schema::hasColumn('policies', 'deductible_amount')) {
                    $table->dropColumn('deductible_amount');
                }
            });
        }

        if (Schema::hasTable('claims')) {
            Schema::table('claims', function (Blueprint $table): void {
                if (Schema::hasColumn('claims', 'protocol_number')) {
                    $table->dropColumn('protocol_number');
                }
                if (Schema::hasColumn('claims', 'claim_type')) {
                    $table->dropColumn('claim_type');
                }
            });
        }

        if (Schema::hasTable('insureds')) {
            Schema::table('insureds', function (Blueprint $table): void {
                if (Schema::hasColumn('insureds', 'birth_date')) {
                    $table->dropColumn('birth_date');
                }
                if (Schema::hasColumn('insureds', 'lead_id')) {
                    $table->dropConstrainedForeignId('lead_id');
                }
            });
        }
    }
};
