<?php

namespace Dearpos\Customer\Database\Factories;

use Dearpos\Customer\Models\Customer;
use Dearpos\Customer\Models\CustomerGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        static $customerNumber = 1;

        return [
            'group_id' => CustomerGroup::factory(),
            'code' => 'CUST' . str_pad($customerNumber++, 3, '0', STR_PAD_LEFT),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('021-########'),
            'mobile' => fake()->numerify('08##########'),
            'tax_number' => fake()->numerify('###########'),
            'credit_limit' => fake()->randomFloat(4, 1000000, 10000000),
            'current_balance' => 0,
            'notes' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['active', 'inactive', 'blocked']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'blocked',
        ]);
    }

    public function withBalance(float $balance): static
    {
        return $this->state(fn (array $attributes) => [
            'current_balance' => $balance,
        ]);
    }
}
