<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Company;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(?int $companyId, ?int $qtyPerCompany)
    {
        $query = Company::query();
        if ($companyId) $query->where('id', '=', $companyId);
        $companies = $query->get();

        if (! $qtyPerCompany) $qtyPerCompany = 5;
        foreach ($companies as $company) {
            for ($i = 0; $i < $qtyPerCompany; $i++) {
                $brandFactory = Brand::factory()->for($company);
                $brandFactory->create();
            }
        }
    }
}
