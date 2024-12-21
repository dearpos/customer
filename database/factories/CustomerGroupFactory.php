<?php

namespace Dearpos\Customer\Database\Factories;

use Dearpos\Customer\Models\CustomerGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerGroupFactory extends Factory
{
    protected $model = CustomerGroup::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'discount_percentage' => fake()->randomFloat(2, 0, 100),
            'is_active' => fake()->boolean(80), // 80% chance of being active
        ];
    }

    public function regular(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Regular',
            'description' => 'Regular customers without special discount',
            'discount_percentage' => 0,
            'is_active' => true,
        ]);
    }

    public function vip(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'VIP',
            'description' => 'VIP customers with special discount',
            'discount_percentage' => 10,
            'is_active' => true,
        ]);
    }
}
