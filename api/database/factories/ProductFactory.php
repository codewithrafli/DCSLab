<?php

namespace Database\Factories;

use App\Enums\ProductCategoryTypeEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\RecordStatusEnum;
use App\Helpers\FactoryHelper;
use App\Models\Brand;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $type = fake()->randomElement(ProductTypeEnum::toArrayEnum());

        $category = (function () use ($type) {
            if ($type == ProductTypeEnum::RAW_MATERIAL || $type == ProductTypeEnum::WORK_IN_PROGRESS || $type == ProductTypeEnum::FINISHED_GOODS) {
                $productCategory = ProductCategory::where('type', ProductCategoryTypeEnum::PRODUCT->value);
                if ($productCategory->exists()) return $productCategory->inRandomOrder()->value('id');

                return ProductCategory::factory()->forProduct()->create();
            }

            if ($type == ProductTypeEnum::SERVICE) {
                $productCategory = ProductCategory::where('type', ProductCategoryTypeEnum::SERVICE->value);
                if ($productCategory->exists()) return $productCategory->inRandomOrder()->value('id');

                return ProductCategory::factory()->forService()->create();
            }
        })();

        $brand = (function () use ($type) {
            if ($type == ProductTypeEnum::RAW_MATERIAL || $type == ProductTypeEnum::WORK_IN_PROGRESS || $type == ProductTypeEnum::FINISHED_GOODS) {
                $brand = Brand::inRandomOrder();
                if ($brand->exists()) return $brand->value('id');

                return Brand::factory()->create();
            }

            if ($type == ProductTypeEnum::SERVICE) return null;
        })();

        $name = (function () use ($category, $brand, $type) {
            if ($type == ProductTypeEnum::RAW_MATERIAL || $type == ProductTypeEnum::WORK_IN_PROGRESS || $type == ProductTypeEnum::FINISHED_GOODS) {
                return $category->name.' '.$brand->name.' '.fake()->words(3, true);
            }

            if ($type == ProductTypeEnum::SERVICE) {
                return $category->name.' '.fake()->words(3, true);
            }
        })();

        return [
            'code' => strtoupper(fake()->lexify()).fake()->numerify(),
            'is_manufacturer_sku' => fake()->boolean(),
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => $name,
            'slug' => fake()->slug(),
            'is_taxable' => fake()->boolean(),
            'vat_rate' => fake()->numberBetween(0, 100),
            'is_price_include_vat' => fake()->boolean(),
            'is_use_serial_number' => fake()->boolean(),
            'is_expirable' => fake()->boolean(),
            'point' => fake()->numberBetween(0, 100),
            'remarks' => fake()->sentence(),
            'type' => $type,
            'status' => fake()->randomElement(RecordStatusEnum::toArrayEnum()),
        ];
    }

    public function setProductTypeEnumAsProduct(bool $encode)
    {
        return $this->state(function (array $attributes) use ($encode) {
            $productCategory = ProductCategory::where('type', ProductCategoryTypeEnum::PRODUCT->value)->inRandomOrder()->first();
            $brand = Brand::inRandomOrder()->first();
            $types = [ProductTypeEnum::RAW_MATERIAL->value, ProductTypeEnum::WORK_IN_PROGRESS->value, ProductTypeEnum::FINISHED_GOODS->value];

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

    public function setProductTypeEnumAsService(bool $encode)
    {
        return $this->state(function (array $attributes) use ($encode) {
            $productCategory = ProductCategory::where('type', ProductCategoryTypeEnum::SERVICE->value)->inRandomOrder()->first();
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
                'type' => ProductTypeEnum::SERVICE->value,
                'product_units' => $productUnits,
            ];

            if ($encode) $result = FactoryHelper::encodeIds($result);

            return $result;
        });
    }
}
