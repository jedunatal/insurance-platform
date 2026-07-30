<?php

namespace Database\Factories;

use App\Enums\LeadSourceEnum;
use App\Enums\LeadStatusEnum;
use App\Models\Product;
use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id'       => Tenant::factory(),
            'product_id'      => Product::factory(),
            'assigned_to'     => User::factory(),
            'created_by'      => User::factory(),
            'name'            => fake()->name(),
            'email'           => fake()->safeEmail(),
            'phone'           => fake()->cellphoneNumber(),
            'document'        => fake()->numerify('###########'),
            'source'          => fake()->randomElement(LeadSourceEnum::cases()),
            'status'          => fake()->randomElement([
                LeadStatusEnum::New,
                LeadStatusEnum::Contact,
                LeadStatusEnum::Proposal,
            ]),
            'next_contact_at' => fake()->optional()->dateTimeBetween('now', '+7 days'),
            'notes'           => fake()->optional()->sentence(),
        ];
    }
}

