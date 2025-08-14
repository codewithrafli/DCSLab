<?php

namespace Tests\Unit\Actions\CapitalWithdrawalActions;

use App\Actions\CapitalWithdrawal\CapitalWithdrawalActions;
use App\Models\Branch;
use App\Models\CapitalWithdrawal;
use App\Models\Company;
use App\Models\User;
use Exception;
use Tests\ActionsTestCase;

class CapitalWithdrawalActionsCreateTest extends ActionsTestCase
{
    private CapitalWithdrawalActions $capitalWithdrawalActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->capitalWithdrawalActions = new CapitalWithdrawalActions();
    }

    public function test_capital_withdrawal_actions_call_create_expect_db_has_record()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()->has(Branch::factory()))
            ->create();

        $company = $user->companies()->inRandomOrder()->first();

        $capitalWithdrawalArr = CapitalWithdrawal::factory()->for($company)
            ->make()->toArray();

        $result = $this->capitalWithdrawalActions->create($capitalWithdrawalArr);

        $this->assertDatabaseHas('capital_withdrawals', [
            'id' => $result->id,
            'company_id' => $capitalWithdrawalArr['company_id'],
            'code' => $capitalWithdrawalArr['code'],
            'name' => $capitalWithdrawalArr['name'],
        ]);
    }

    public function test_capital_withdrawal_actions_call_create_with_empty_array_parameters_expect_exception()
    {
        $this->expectException(Exception::class);
        $this->capitalWithdrawalActions->create([]);
    }
}
