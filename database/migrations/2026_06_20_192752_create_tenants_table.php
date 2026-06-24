<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table): void {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            
            // Novos campos adicionados
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('document', 20)->unique(); // CPF ou CNPJ
            $table->string('plan')->default('classic'); // ou 'free', 'premium', etc.

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};