<?php

namespace Database\Factories;

use App\Enums\PaymentTermType;
use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->lexify()).fake()->numerify(),
            'is_member' => fake()->boolean(),
            'name' => fake()->name(),
            'zone' => fake()->city(),
            'max_open_invoice' => fake()->randomNumber(),
            'max_outstanding_invoice' => fake()->randomNumber(),
            'max_invoice_age' => fake()->randomNumber(),
            'payment_term_type' => fake()->randomElement(PaymentTermType::toArrayEnum()),
            'payment_term' => fake()->randomNumber(),
            'taxable_enterprise' => fake()->boolean(),
            'tax_id' => fake()->numberBetween(0, 100) * 10000,
            'status' => fake()->randomElement(RecordStatus::toArrayEnum()),
            'remarks' => fake()->sentence(),
        ];
    }
}
