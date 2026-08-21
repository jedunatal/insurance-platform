<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela de Notificações do Sistema (Bell Notifications)
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. Atualização na tabela 'policies' para Suporte a Renovações, Split de Comissão e Dados Específicos por Ramo
        if (Schema::hasTable('policies')) {
            Schema::table('policies', function (Blueprint $table): void {
                if (! Schema::hasColumn('policies', 'previous_policy_id')) {
                    $table->foreignId('previous_policy_id')->nullable()->after('insured_id')->constrained('policies')->nullOnDelete();
                }
                if (! Schema::hasColumn('policies', 'renewal_status')) {
                    $table->string('renewal_status')->default('not_started')->after('status')->index();
                }
                if (! Schema::hasColumn('policies', 'producer_id')) {
                    $table->foreignId('producer_id')->nullable()->after('broker_id')->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('policies', 'producer_commission_percentage')) {
                    $table->decimal('producer_commission_percentage', 5, 2)->default(0)->after('commission_amount');
                }
                if (! Schema::hasColumn('policies', 'producer_commission_amount')) {
                    $table->decimal('producer_commission_amount', 14, 2)->default(0)->after('producer_commission_percentage');
                }
                if (! Schema::hasColumn('policies', 'vehicle_data')) {
                    $table->json('vehicle_data')->nullable()->after('insured_object');
                }
                if (! Schema::hasColumn('policies', 'property_data')) {
                    $table->json('property_data')->nullable()->after('vehicle_data');
                }
                if (! Schema::hasColumn('policies', 'beneficiaries')) {
                    $table->json('beneficiaries')->nullable()->after('property_data');
                }
            });
        }

        // 3. Tabela da Esteira de Renovações (Policy Renewals Pipeline)
        if (! Schema::hasTable('policy_renewals')) {
            Schema::create('policy_renewals', function (Blueprint $table): void {
                $table->id();

                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->foreignId('policy_id')->constrained('policies')->cascadeOnDelete();
                $table->foreignId('insured_id')->nullable()->constrained('insureds')->nullOnDelete();
                $table->foreignId('renewed_policy_id')->nullable()->constrained('policies')->nullOnDelete();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

                $table->string('stage')->default('to_contact')->index(); // to_contact, in_quotation, proposal_sent, renewed, lost
                $table->string('loss_reason')->nullable();               // price, asset_sold, competitor, dissatisfaction, other
                $table->text('loss_notes')->nullable();
                $table->date('target_date')->index();
                $table->text('notes')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['tenant_id', 'stage']);
                $table->index(['target_date', 'stage']);
            });
        }

        // 4. Tabela de Cotações (Quotes)
        if (! Schema::hasTable('quotes')) {
            Schema::create('quotes', function (Blueprint $table): void {
                $table->id();

                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
                $table->foreignId('insured_id')->nullable()->constrained('insureds')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('converted_policy_id')->nullable()->constrained('policies')->nullOnDelete();

                $table->string('quote_number')->unique();
                $table->string('title');
                $table->string('branch')->index();
                $table->string('status')->default('draft')->index(); // draft, sent, approved, rejected, converted, expired
                $table->date('valid_until')->nullable();

                $table->json('risk_data')->nullable(); // dados do risco (veículo, imóvel, etc.)
                $table->text('notes')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'branch']);
            });
        }

        // 5. Tabela de Opções da Cotação (Quote Options para Comparativo Multi-Seguradoras)
        if (! Schema::hasTable('quote_options')) {
            Schema::create('quote_options', function (Blueprint $table): void {
                $table->id();

                $table->foreignId('quote_id')->constrained('quotes')->cascadeOnDelete();

                $table->string('insurer'); // Porto Seguro, Allianz, etc.
                $table->string('product_name')->nullable();
                $table->decimal('net_premium', 14, 2)->default(0);
                $table->decimal('iof_amount', 14, 2)->default(0);
                $table->decimal('total_premium', 14, 2)->default(0);

                $table->string('deductible_type')->default('normal'); // normal, reduzida, isenta
                $table->decimal('deductible_amount', 14, 2)->default(0);

                $table->string('car_rental')->nullable(); // sem_carro, 7_dias, 15_dias, 30_dias, ilimitado
                $table->string('glass_coverage')->nullable(); // basica, completa, blindados
                $table->decimal('third_party_materials', 14, 2)->default(0);
                $table->decimal('third_party_corporal', 14, 2)->default(0);
                $table->decimal('app_coverage', 14, 2)->default(0);

                $table->string('payment_conditions')->nullable(); // ex: 10x sem juros no cartão
                $table->boolean('is_recommended')->default(false);
                $table->boolean('is_accepted')->default(false);
                $table->text('highlights')->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();
            });
        }

        // 6. Tabela de Gestão Eletrônica de Documentos (GED / Attachments)
        if (! Schema::hasTable('attachments')) {
            Schema::create('attachments', function (Blueprint $table): void {
                $table->id();

                $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
                $table->morphs('attachable'); // attachable_type, attachable_id (Insured, Policy, Claim, Lead)
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

                $table->string('title');
                $table->string('category')->default('other')->index(); // cnh, crlv, apolice, vistoria, bo, orcamento, foto, comprovante, outro
                $table->string('file_path');
                $table->string('file_name');
                $table->string('file_type')->nullable(); // mime type (pdf, png, jpeg, etc.)
                $table->unsignedBigInteger('file_size')->default(0); // bytes
                $table->text('notes')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['tenant_id', 'category']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('quote_options');
        Schema::dropIfExists('quotes');
        Schema::dropIfExists('policy_renewals');
        Schema::dropIfExists('notifications');

        if (Schema::hasTable('policies')) {
            Schema::table('policies', function (Blueprint $table): void {
                $columns = [
                    'beneficiaries',
                    'property_data',
                    'vehicle_data',
                    'producer_commission_amount',
                    'producer_commission_percentage',
                    'producer_id',
                    'renewal_status',
                    'previous_policy_id',
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('policies', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
