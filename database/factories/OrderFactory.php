<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Tryout;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'tryout_id' => Tryout::factory(),
            'amount' => 50000,
            'discount' => 0,
            'total' => 50000,
            'status' => Order::STATUS_PENDING,
            'gateway' => 'midtrans',
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Order::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }
}
