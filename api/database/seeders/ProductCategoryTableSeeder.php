<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class ProductCategoryTableSeeder extends Seeder
{
    /**
     * Seed product categories for companies.
     *
     * @param  int|null  $companyId  Specific company ID to seed categories for
     * @param  int  $qtyPerCompany  Number of categories to create per company
     */
    public function run(?int $companyId = null, int $qtyPerCompany = 5): void
    {
        $companies = $companyId ? Company::where('id', $companyId)->get() : Company::all();

        foreach ($companies as $company) {
            ProductCategory::factory()
                ->count($qtyPerCompany)
                ->for($company)
                ->create();
        }
    }
}
