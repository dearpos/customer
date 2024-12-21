<?php

namespace Dearpos\Customer\Database\Factories;

use Dearpos\Customer\Models\Customer;
use Dearpos\Customer\Models\CustomerContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerContactFactory extends Factory
{
    protected $model = CustomerContact::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'name' => fake()->name(),
            'position' => fake()->jobTitle(),
            'phone' => fake()->numerify('021-########'),
            'mobile' => fake()->numerify('08##########'),
            'email' => fake()->unique()->safeEmail(),
            'is_primary' => fake()->boolean(20), // 20% chance of being primary
        ];
    }

    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    public function purchasingManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Jane Doe',
            'position' => 'Purchasing Manager',
            'phone' => '021-5555556',
            'mobile' => '08123456780',
            'email' => 'jane.doe@example.com',
            'is_primary' => true,
        ]);
    }
}
