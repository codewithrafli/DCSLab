<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Investor;
use Illuminate\Database\Seeder;

class InvestorSeeder extends Seeder
{
    public function run(?int $companyId, ?int $qtyPerCompany)
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
                $investorFactory = Investor::factory()->for($company);
                $investorFactory->create();
            }
        }
    }
}
