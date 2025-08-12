<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CapitalAdditionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->lexify()).fake()->numerify(),
            'date' => fake()->date(),
            'amount' => fake()->numberBetween(0, 100),
            'remarks' => fake()->sentence(),
        ];
    }
}
