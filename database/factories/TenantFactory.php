<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name'      => $name,
            'slug'       => Str::slug($name),
            'email'      => fake()->companyEmail(),
            'phone'      => fake()->phoneNumber(),
            'document'   => fake()->numerify('############'),
            'plan'       => fake()->randomElement(['starter', 'pro', 'enterprise']),
            'is_active'  => true,
        ];
    }
}
