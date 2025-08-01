<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BrandFactory extends Factory
{
    public function definition(): array
    {
        $brands = [
            'Samsung', 'Huawei', 'LV', 'Apple', 'Xiaomi', 'Oppo',
            'Vivo', 'Google', 'OnePlus', 'Motorola', 'Nokia', 'Sony',
            'LG', 'TCL', 'Hisense', 'Sharp',
        ];

        return [
            'code' => strtoupper(fake()->lexify()).fake()->numerify(),
            'name' => $brands[array_rand($brands)],
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
        $text = fake()->randomElement(['Samsung', 'Huawei', 'LV']);

        return substr_replace($text, $str, random_int(0, strlen($text) - 1), 0);
    }
}
