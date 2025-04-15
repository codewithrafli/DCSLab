<?php

namespace Tests\Unit\Actions\CapitalWithdrawalActions;

use App\Actions\CapitalWithdrawal\CapitalWithdrawalActions;
use App\Models\CapitalWithdrawal;
use App\Models\Company;
use App\Models\User;
use Tests\ActionsTestCase;

class CapitalWithdrawalActionsDeleteTest extends ActionsTestCase
{
    private CapitalWithdrawalActions $capitalWithdrawalActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->capitalWithdrawalActions = new CapitalWithdrawalActions();
    }

    public function test_capital_withdrawal_actions_call_delete_expect_bool()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(CapitalWithdrawal::factory())
            )->create();

        $capitalWithdrawal = $user->companies()->inRandomOrder()->first()
            ->capitalWithdrawals()->inRandomOrder()->first();
        $result = $this->capitalWithdrawalActions->delete($capitalWithdrawal);

        $this->assertIsBool($result);
        $this->assertTrue($result);
        $this->assertSoftDeleted('capital_withdrawals', [
            'id' => $capitalWithdrawal->id,
        ]);
    }
}
