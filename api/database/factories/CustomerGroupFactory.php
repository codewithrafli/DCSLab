<?php

namespace Database\Factories;

use App\Enums\PaymentTermTypeEnum;
use App\Enums\RoundingTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'payment_term_type' => fake()->randomElement(PaymentTermTypeEnum::toArrayEnum()),
            'payment_term' => fake()->numberBetween(1, 100),
            'selling_point' => fake()->numberBetween(0, 5),
            'selling_point_multiple' => fake()->numberBetween(0, 100) * 10000,
            'sell_at_cost' => fake()->boolean(),
            'price_markup_percent' => fake()->numberBetween(0, 100),
            'price_markup_nominal' => fake()->numberBetween(0, 100) * 10000,
            'price_markdown_percent' => fake()->numberBetween(0, 100),
            'price_markdown_nominal' => fake()->numberBetween(0, 100) * 10000,
            'round_on' => fake()->randomElement(RoundingTypeEnum::toArrayEnum()),
            'round_digit' => fake()->numberBetween(0, 100),
            'remarks' => fake()->sentence(),
        ];
    }

    public function insertStringInName(string $str)
    {
        return $this->state(function (array $attributes) use ($str) {
            return [
                'name' => $this->craftName($str),
            ];
        });
    }

    private function craftName(string $str)
    {
        $faker = \Faker\Factory::create('id_ID');
        $text = Str::random(10);

        return substr_replace($text, $str, random_int(0, strlen($text) - 1), 0);
    }
}
