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
            'is_manufacturer_sku' => fake()->boolean(),
            'name' => fake()->words(3, true),
            'slug' => fake()->slug(),
            'is_taxable' => fake()->boolean(),
            'vat_rate' => fake()->numberBetween(0, 100),
            'is_price_include_vat' => fake()->boolean(),
            'is_use_serial_number' => fake()->boolean(),
            'is_expirable' => fake()->boolean(),
            'point' => fake()->numberBetween(0, 100),
            'remarks' => fake()->sentence(),
            'type' => fake()->randomElement(ProductType::toArrayEnum()),
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
