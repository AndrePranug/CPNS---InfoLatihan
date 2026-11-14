<?php

namespace Database\Factories;

use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('PROMO###??')),
            'type' => Voucher::TYPE_PERCENT,
            'value' => 20,
            'max_uses' => null,
            'used_count' => 0,
            'is_active' => true,
        ];
    }
}
