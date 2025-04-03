<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CashAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->lexify()).fake()->numerify(),
            'name' => fake()->randomElement(['Cash', 'Bank', 'Giro']),
            'is_bank' => fake()->boolean(),
            'is_active' => fake()->boolean(),
            'remarks' => fake()->sentence(),
        ];
    }
}
