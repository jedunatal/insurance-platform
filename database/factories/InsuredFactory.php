<?php

namespace Database\Factories;

use App\Enums\PersonTypeEnum;
use App\Models\Insured;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Insured>
 */
class InsuredFactory extends Factory
{
    public function definition(): array
    {
        $personType = fake()->randomElement(PersonTypeEnum::cases());
        $isIndividual = $personType === PersonTypeEnum::Individual;

        return [
            'tenant_id'    => Tenant::factory(),
            'lead_id'      => null,
            'assigned_to'  => User::factory(),
            'created_by'   => User::factory(),
            'name'         => $isIndividual ? fake()->name() : fake()->company(),
            'email'        => $isIndividual ? fake()->safeEmail() : fake()->companyEmail(),
            'phone'        => fake()->cellphoneNumber(),
            'document'     => $isIndividual
                ? fake()->numerify('###########')
                : fake()->numerify('##############'),
            'person_type'  => $personType->value,
            'zip_code'     => fake()->postcode(),
            'address'      => fake()->streetName(),
            'number'       => fake()->buildingNumber(),
            'complement'   => fake()->optional()->secondaryAddress(),
            'neighborhood' => fake()->citySuffix(),
            'city'         => fake()->city(),
            'state'        => fake()->stateAbbr(),
            'notes'        => fake()->optional()->sentence(),
        ];
    }

    public function individual(): static
    {
        return $this->state(fn () => [
            'person_type' => PersonTypeEnum::Individual->value,
            'name'     => fake()->name(),
            'email'    => fake()->safeEmail(),
            'document' => fake()->numerify('###########'),
        ]);
    }

    public function legal(): static
    {
        return $this->state(fn () => [
            'person_type' => PersonTypeEnum::Legal->value,
            'name'     => fake()->company(),
            'email'    => fake()->companyEmail(),
            'document' => fake()->numerify('##############'),
        ]);
    }
}
