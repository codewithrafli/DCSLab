<?php

namespace Database\Seeders;

use App\Models\CashAccount;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CashAccountSeeder extends Seeder
{
    public function run(?int $cashAccountsPerCompany = null, ?int $companyId = null)
    {
        $cashAccountsPerCompany = $cashAccountsPerCompany ?? 5;

        $companies = $companyId ? Company::where('id', $companyId)->get() : Company::all();

        foreach ($companies as $company) {
            for ($i = 0; $i < $cashAccountsPerCompany; $i++) {
                CashAccount::factory()
                    ->for($company)
                    ->state(function (array $attributes, Company $company) {
                        return [
                            'branch_id' => $company->branches->random()->id,
                        ];
                    })
                    ->create();
            }
        }
    }
}
