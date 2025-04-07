<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseAdditionalCostCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->lexify()).fake()->numerify(),
            'name' => fake()->text(),
        ];
    }
}
