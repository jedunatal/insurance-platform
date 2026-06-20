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

            $table->foreignId('tenant_id')
                ->constrained('tenants')
                ->cascadeOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('phone', 20)->nullable()->index();

            $table->string('source', 30);

            $table->string('status', 30)
                ->default('novo');

            $table->dateTime('next_contact_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['tenant_id', 'status'],
                'leads_tenant_status_idx'
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