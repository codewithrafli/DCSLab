<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(?int $unitsPerCompany = null, ?int $companyId = null)
    {
        $unitsPerCompany = $unitsPerCompany ?? 5;

        $companies = $companyId ? Company::where('id', $companyId)->get() : Company::all();

        foreach ($companies as $company) {
            for ($i = 0; $i < $unitsPerCompany; $i++) {
                Unit::factory()
                    ->for($company)
                    ->create();
            }
        }
    }
}
