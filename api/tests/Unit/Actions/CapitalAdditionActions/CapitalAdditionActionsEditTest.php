<?php

namespace Tests\Unit\Actions\CapitalAdditionActions;

use App\Actions\CapitalAddition\CapitalAdditionActions;
use App\Models\CapitalAddition;
use App\Models\Company;
use App\Models\User;
use Exception;
use Tests\ActionsTestCase;

class CapitalAdditionActionsEditTest extends ActionsTestCase
{
    private CapitalAdditionActions $capitalAdditionActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->capitalAdditionActions = new CapitalAdditionActions();
    }

    public function test_capital_addition_actions_call_update_expect_db_updated()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(CapitalAddition::factory())
            )->create();

        $company = $user->companies()->inRandomOrder()->first();
        $capitalAddition = $company->capitalAdditions()->inRandomOrder()->first();

        $capitalAdditionArr = CapitalAddition::factory()->make()->toArray();

        $result = $this->capitalAdditionActions->update($capitalAddition, $capitalAdditionArr);

        $this->assertInstanceOf(CapitalAddition::class, $result);
        $this->assertDatabaseHas('capital_additions', [
            'id' => $capitalAddition->id,
            'company_id' => $capitalAddition->company_id,
            'code' => $capitalAdditionArr['code'],
            'name' => $capitalAdditionArr['name'],
        ]);
    }

    public function test_capital_addition_actions_call_update_with_empty_array_parameters_expect_exception()
    {
        $this->expectException(Exception::class);

        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(CapitalAddition::factory())
            )->create();

        $capitalAddition = $user->companies()->inRandomOrder()->first()
            ->capitalAdditions()->inRandomOrder()->first();

        $capitalAdditionArr = [];

        $this->capitalAdditionActions->update($capitalAddition, $capitalAdditionArr);
    }
}
