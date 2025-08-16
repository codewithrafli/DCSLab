<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CustomerGroup;
use Illuminate\Database\Seeder;

class CustomerGroupSeeder extends Seeder
{
    public function run(?int $companyId = null, ?int $qtyPerCompany = null)
    {
        $query = Company::query();
        if ($companyId) {
            $query->where('id', '=', $companyId);
        }
        $companies = $query->get();

        if (! $qtyPerCompany) {
            $qtyPerCompany = 5;
        }
        foreach ($companies as $company) {
            for ($i = 0; $i < $qtyPerCompany; $i++) {
                $customerGroupFactory = CustomerGroup::factory()->for($company);
                $customerGroupFactory->create();
            }
        }
    }
}
