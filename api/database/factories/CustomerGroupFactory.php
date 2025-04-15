<?php

namespace Database\Factories;

use App\Enums\PaymentTermType;
use App\Enums\RoundOn;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerGroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->lexify()).fake()->numerify(),
            'name' => fake()->randomElement(['Grosir', 'Semi Grosir', 'Umum']),
            'max_open_invoice' => fake()->numberBetween(1, 100),
            'max_outstanding_invoice' => fake()->numberBetween(0, 100) * 10000,
            'max_invoice_age' => fake()->numberBetween(1, 100),
            'payment_term_type' => fake()->randomElement(PaymentTermType::toArrayEnum()),
            'payment_term' => fake()->numberBetween(1, 100),
            'selling_point' => fake()->numberBetween(0, 100) * 10000,
            'selling_point_multiple' => fake()->numberBetween(0, 100) * 10000,
            'sell_at_cost' => fake()->boolean(),
            'price_markup_percent' => fake()->numberBetween(0, 100),
            'price_markup_nominal' => fake()->numberBetween(0, 100) * 10000,
            'price_markdown_percent' => fake()->numberBetween(0, 100),
            'price_markdown_nominal' => fake()->numberBetween(0, 100) * 10000,
            'round_on' => fake()->randomElement(RoundOn::toArrayEnum()),
            'round_digit' => fake()->numberBetween(0, 100),
            'remarks' => fake()->sentence(),
        ];
    }
}
