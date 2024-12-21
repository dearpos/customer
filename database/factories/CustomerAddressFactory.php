<?php

namespace Dearpos\Customer\Database\Factories;

use Dearpos\Customer\Models\Customer;
use Dearpos\Customer\Models\CustomerAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerAddressFactory extends Factory
{
    protected $model = CustomerAddress::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'address_type' => fake()->randomElement(['billing', 'shipping', 'both']),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->optional()->secondaryAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->country(),
            'is_default' => fake()->boolean(20), // 20% chance of being default
        ];
    }

    public function billing(): static
    {
        return $this->state(fn (array $attributes) => [
            'address_type' => 'billing',
        ]);
    }

    public function shipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'address_type' => 'shipping',
        ]);
    }

    public function both(): static
    {
        return $this->state(fn (array $attributes) => [
            'address_type' => 'both',
        ]);
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
