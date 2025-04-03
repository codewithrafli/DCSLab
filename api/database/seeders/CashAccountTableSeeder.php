<?php

namespace Database\Seeders;

use App\Models\CashAccount;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CashAccountTableSeeder extends Seeder
{
    public function run(?int $companyId, ?int $qtyPerCompany)
    {
        $query = Company::query();
        if ($companyId) $query->where('id', '=', $companyId);
        $companies = $query->get();

        if (! $qtyPerCompany) $qtyPerCompany = 5;
        foreach ($companies as $company) {
            for ($i = 0; $i < $qtyPerCompany; $i++) {
                $cashAccountFactory = CashAccount::factory()->for($company);
                $cashAccountFactory->create();
            }
        }
    }
}
