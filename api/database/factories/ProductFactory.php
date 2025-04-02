<?php

namespace Database\Factories;

use App\Enums\ProductCategoryType;
use App\Enums\ProductType;
use App\Enums\RecordStatus;
use App\Helpers\FactoryHelper;
use App\Models\Brand;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->lexify()).fake()->numerify(),
            'is_factory_code' => fake()->boolean(),
            'name' => fake()->words(3, true),
            'slug' => fake()->slug(),
            'taxable_supply' => fake()->boolean(),
            'standard_rated_supply' => fake()->numberBetween(0, 100),
            'price_include_vat' => fake()->boolean(),
            'point' => fake()->numberBetween(0, 100),
            'use_serial_number' => fake()->boolean(),
            'has_expiry_date' => fake()->boolean(),
            'type' => fake()->randomElement(ProductType::toArrayEnum()),
            'remarks' => fake()->sentence(),
            'status' => fake()->randomElement(RecordStatus::toArrayEnum()),
        ];
    }

    public function setProductTypeAsProduct(bool $encode)
    {
        return $this->state(function (array $attributes) use ($encode) {
            $productCategory = ProductCategory::where('type', ProductCategoryType::PRODUCT->value)->inRandomOrder()->first();
            $brand = Brand::inRandomOrder()->first();
            $types = [ProductType::RAW_MATERIAL->value, ProductType::WORK_IN_PROGRESS->value, ProductType::FINISHED_GOODS->value];

            $productUnits = (function () use ($encode) {
                $productUnits = ProductUnit::factory()->count(mt_rand(1, 3))->make()->toArray();

                foreach ($productUnits as $productUnit) {
                    if ($encode) $productUnit = FactoryHelper::encodeIds($productUnit);
                }

                return $productUnits;
            })();

            $result = [
                'category_id' => $productCategory->id,
                'brand_id' => $brand->id,
                'type' => fake()->randomElement($types),
                'product_units' => $productUnits,
            ];

            if ($encode) $result = FactoryHelper::encodeIds($result);

            return $result;
        });
    }

    public function setProductTypeAsService(bool $encode)
    {
        return $this->state(function (array $attributes) use ($encode) {
            $productCategory = ProductCategory::where('type', ProductCategoryType::SERVICE->value)->inRandomOrder()->first();
            $brand = Brand::inRandomOrder()->first();

            $productUnits = (function () use ($encode) {
                $productUnits = ProductUnit::factory()->make([
                    'is_base' => true,
                    'conversion_value' => 1,
                    'is_primary_unit' => true,
                ])->toArray();

                foreach ($productUnits as $productUnit) {
                    if ($encode) $productUnit = FactoryHelper::encodeIds($productUnit);
                }

                return $productUnits;
            })();

            $result = [
                'category_id' => $productCategory->id,
                'brand_id' => mt_rand(0, 1) ? $brand->id : null,
                'type' => ProductType::SERVICE->value,
                'product_units' => $productUnits,
            ];

            if ($encode) $result = FactoryHelper::encodeIds($result);

            return $result;
        });
    }
}
