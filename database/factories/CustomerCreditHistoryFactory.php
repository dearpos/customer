<?php

namespace Dearpos\Customer\Database\Factories;

use App\Models\User;
use Dearpos\Customer\Models\Customer;
use Dearpos\Customer\Models\CustomerCreditHistory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomerCreditHistoryFactory extends Factory
{
    protected $model = CustomerCreditHistory::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'transaction_type' => fake()->randomElement(['increase', 'decrease', 'adjustment']),
            'amount' => fake()->randomFloat(4, 100000, 1000000),
            'reference_type' => fake()->randomElement(['sales_order', 'payment', 'credit_note', 'manual']),
            'reference_id' => Str::uuid(),
            'notes' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }

    public function increase(): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => 'increase',
        ]);
    }

    public function decrease(): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => 'decrease',
        ]);
    }

    public function adjustment(): static
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => 'adjustment',
            'notes' => 'Manual adjustment by admin',
        ]);
    }

    public function salesOrder(): static
    {
        return $this->state(fn (array $attributes) => [
            'reference_type' => 'sales_order',
            'notes' => 'Sales order transaction',
        ]);
    }
}
