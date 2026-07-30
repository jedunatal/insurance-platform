<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            'Seguro Auto',
            'Seguro de Vida',
            'Seguro Residencial',
            'Seguro Empresarial',
            'Seguro de Saúde',
            'Seguro Rural',
            'Seguro de Viagem',
        ]);

        return [
            'tenant_id' => Tenant::factory(),
            'name'      => $name,
            'is_active' => true,
        ];
    }
}
