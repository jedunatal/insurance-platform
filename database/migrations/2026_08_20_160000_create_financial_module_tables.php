<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Atualizações e garantias na tabela 'policies'
        if (Schema::hasTable('policies')) {
            Schema::table('policies', function (Blueprint $table): void {
                if (! Schema::hasColumn('policies', 'iof_rate')) {
                    $table->decimal('iof_rate', 5, 2)->default(7.38)->after('net_premium');
                }
                if (! Schema::hasColumn('policies', 'commission_percentage')) {
                    $table->decimal('commission_percentage', 5, 2)->default(0)->after('total_premium');
                }
                if (! Schema::hasColumn('policies', 'commission_amount')) {
                    $table->decimal('commission_amount', 14, 2)->default(0)->after('commission_percentage');
                }
            });
        }

        // 2. Criação da tabela 'policy_installments'
        if (! Schema::hasTable('policy_installments')) {
            Schema::create('policy_installments', function (Blueprint $table): void {
                $table->id();

                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->foreignId('policy_id')->constrained('policies')->cascadeOnDelete();
                $table->foreignId('insured_id')->nullable()->constrained('insureds')->nullOnDelete();

                $table->unsignedSmallInteger('installment_number');
                $table->unsignedSmallInteger('total_installments');
                $table->date('due_date')->index();
                $table->date('payment_date')->nullable()->index();

                $table->decimal('gross_amount', 14, 2)->default(0);
                $table->decimal('commission_expected', 14, 2)->default(0);
                $table->decimal('commission_received', 14, 2)->nullable();

                $table->string('status')->default('pending')->index();
                $table->text('notes')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['tenant_id', 'status']);
                $table->index(['policy_id', 'installment_number']);
                $table->index(['due_date', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_installments');

        if (Schema::hasTable('policies')) {
            Schema::table('policies', function (Blueprint $table): void {
                if (Schema::hasColumn('policies', 'commission_amount')) {
                    $table->dropColumn('commission_amount');
                }
                if (Schema::hasColumn('policies', 'commission_percentage')) {
                    $table->dropColumn('commission_percentage');
                }
                if (Schema::hasColumn('policies', 'iof_rate')) {
                    $table->dropColumn('iof_rate');
                }
            });
        }
    }
};
