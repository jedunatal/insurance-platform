<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Seguro Auto', 'tenant_id' => 1, 'is_active' => true],
            ['name' => 'Seguro de Vida', 'tenant_id' => 1, 'is_active' => true],
            ['name' => 'Seguro Residencial', 'tenant_id' => 1, 'is_active' => true],
            ['name' => 'Seguro Celular / Portáteis', 'tenant_id' => 1, 'is_active' => true],
            ['name' => 'Plano de Saúde / Odonto', 'tenant_id' => 1, 'is_active' => true],
            ['name' => 'Seguro Empresarial', 'tenant_id' => 1, 'is_active' => true],
            ['name' => 'Responsabilidade Civil', 'tenant_id' => 1, 'is_active' => true],
        ];

        foreach ($products as $product) {
            Product::withoutGlobalScopes()->firstOrCreate(
                ['tenant_id' => $product['tenant_id'], 'name' => $product['name']],
                $product
            );
        }
    }
}