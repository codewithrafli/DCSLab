<?php

namespace Tests\Unit\Actions\CapitalWithdrawalActions;

use App\Actions\CapitalWithdrawal\CapitalWithdrawalActions;
use App\Models\CapitalWithdrawal;
use App\Models\Company;
use App\Models\User;
use Exception;
use Tests\ActionsTestCase;

class CapitalWithdrawalActionsEditTest extends ActionsTestCase
{
    private CapitalWithdrawalActions $capitalWithdrawalActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->capitalWithdrawalActions = new CapitalWithdrawalActions();
    }

    public function test_capital_withdrawal_actions_call_update_expect_db_updated()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(CapitalWithdrawal::factory())
            )->create();

        $company = $user->companies()->inRandomOrder()->first();
        $capitalWithdrawal = $company->capitalWithdrawals()->inRandomOrder()->first();

        $capitalWithdrawalArr = CapitalWithdrawal::factory()->make()->toArray();

        $result = $this->capitalWithdrawalActions->update($capitalWithdrawal, $capitalWithdrawalArr);

        $this->assertInstanceOf(CapitalWithdrawal::class, $result);
        $this->assertDatabaseHas('capital_withdrawals', [
            'id' => $capitalWithdrawal->id,
            'company_id' => $capitalWithdrawal->company_id,
            'code' => $capitalWithdrawalArr['code'],
            'name' => $capitalWithdrawalArr['name'],
        ]);
    }

    public function test_capital_withdrawal_actions_call_update_with_empty_array_parameters_expect_exception()
    {
        $this->expectException(Exception::class);

        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(CapitalWithdrawal::factory())
            )->create();

        $capitalWithdrawal = $user->companies()->inRandomOrder()->first()
            ->capitalWithdrawals()->inRandomOrder()->first();

        $capitalWithdrawalArr = [];

        $this->capitalWithdrawalActions->update($capitalWithdrawal, $capitalWithdrawalArr);
    }
}
