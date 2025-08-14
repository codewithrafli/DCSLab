<?php

namespace Tests\Unit\Actions\NonCapitalWithdrawalActions;

use App\Actions\NonCapitalWithdrawal\NonCapitalWithdrawalActions;
use App\Models\Branch;
use App\Models\Company;
use App\Models\NonCapitalWithdrawal;
use App\Models\User;
use Exception;
use Tests\ActionsTestCase;

class NonCapitalWithdrawalActionsCreateTest extends ActionsTestCase
{
    private NonCapitalWithdrawalActions $nonCapitalWithdrawalActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->nonCapitalWithdrawalActions = new NonCapitalWithdrawalActions();
    }

    public function test_non_capital_withdrawal_actions_call_create_expect_db_has_record()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()->has(Branch::factory()->setStatusActive()->setIsMainBranch()))
            ->create();

        $company = $user->companies()->inRandomOrder()->first();
        $branch = $company->branches()->inRandomOrder()->first();

        $nonCapitalWithdrawalArr = NonCapitalWithdrawal::factory()->for($company)
            ->make()->toArray();

        $result = $this->nonCapitalWithdrawalActions->create($nonCapitalWithdrawalArr);

        $this->assertDatabaseHas('non_capital_withdrawals', [
            'id' => $result->id,
            'company_id' => $nonCapitalWithdrawalArr['company_id'],
            'code' => $nonCapitalWithdrawalArr['code'],
            'name' => $nonCapitalWithdrawalArr['name'],
        ]);
    }

    public function test_non_capital_withdrawal_actions_call_create_with_empty_array_parameters_expect_exception()
    {
        $this->expectException(Exception::class);
        $this->nonCapitalWithdrawalActions->create([]);
    }
}
