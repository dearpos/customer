<?php

namespace Dearpos\Customer\Database\Factories;

use App\Models\User;
use Dearpos\Customer\Models\Customer;
use Dearpos\Customer\Models\CustomerAudit;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerAuditFactory extends Factory
{
    protected $model = CustomerAudit::class;

    public function definition(): array
    {
        $customer = Customer::factory()->create();
        $oldValues = [
            'name' => fake()->name(),
            'email' => fake()->email(),
        ];
        $newValues = [
            'name' => fake()->name(),
            'email' => fake()->email(),
        ];

        return [
            'auditable_type' => Customer::class,
            'auditable_id' => $customer->id,
            'event' => fake()->randomElement(['created', 'updated', 'deleted', 'status_changed', 'credit_changed']),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => User::factory(),
            'created_at' => fake()->dateTimeThisYear(),
        ];
    }

    public function created(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => 'created',
            'old_values' => null,
        ]);
    }

    public function updated(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => 'updated',
        ]);
    }

    public function deleted(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => 'deleted',
            'new_values' => null,
        ]);
    }

    public function statusChanged(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => 'status_changed',
            'old_values' => ['status' => 'active'],
            'new_values' => ['status' => 'blocked'],
        ]);
    }

    public function creditChanged(): static
    {
        return $this->state(fn (array $attributes) => [
            'event' => 'credit_changed',
            'old_values' => ['credit_limit' => 1000000],
            'new_values' => ['credit_limit' => 2000000],
        ]);
    }
}
