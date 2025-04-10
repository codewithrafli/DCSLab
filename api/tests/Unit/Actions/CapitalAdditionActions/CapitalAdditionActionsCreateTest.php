<?php

namespace Tests\Unit\Actions\CapitalAdditionActions;

use App\Actions\CapitalAddition\CapitalAdditionActions;
use App\Models\CapitalAddition;
use App\Models\Company;
use App\Models\User;
use Exception;
use Tests\ActionsTestCase;

class CapitalAdditionActionsCreateTest extends ActionsTestCase
{
    private CapitalAdditionActions $capitalAdditionActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->capitalAdditionActions = new CapitalAdditionActions();
    }

    public function test_capital_addition_actions_call_create_expect_db_has_record()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $company = $user->companies()->inRandomOrder()->first();

        $capitalAdditionArr = CapitalAddition::factory()->for($company)
            ->make()->toArray();

        $result = $this->capitalAdditionActions->create($capitalAdditionArr);

        $this->assertDatabaseHas('capital_additions', [
            'id' => $result->id,
            'company_id' => $capitalAdditionArr['company_id'],
            'code' => $capitalAdditionArr['code'],
            'name' => $capitalAdditionArr['name'],
        ]);
    }

    public function test_capital_addition_actions_call_create_with_empty_array_parameters_expect_exception()
    {
        $this->expectException(Exception::class);
        $this->capitalAdditionActions->create([]);
    }
}
