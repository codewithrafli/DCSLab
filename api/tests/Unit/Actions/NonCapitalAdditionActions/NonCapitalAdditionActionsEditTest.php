<?php

namespace Tests\Unit\Actions\NonCapitalAdditionActions;

use App\Actions\NonCapitalAddition\NonCapitalAdditionActions;
use App\Models\Company;
use App\Models\NonCapitalAddition;
use App\Models\User;
use Exception;
use Tests\ActionsTestCase;

class NonCapitalAdditionActionsEditTest extends ActionsTestCase
{
    private NonCapitalAdditionActions $nonCapitalAdditionActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->nonCapitalAdditionActions = new NonCapitalAdditionActions();
    }

    public function test_non_capital_addition_actions_call_update_expect_db_updated()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(NonCapitalAddition::factory())
            )->create();

        $company = $user->companies()->inRandomOrder()->first();
        $nonCapitalAddition = $company->nonCapitalAdditions()->inRandomOrder()->first();

        $nonCapitalAdditionArr = NonCapitalAddition::factory()->make()->toArray();

        $result = $this->nonCapitalAdditionActions->update($nonCapitalAddition, $nonCapitalAdditionArr);

        $this->assertInstanceOf(NonCapitalAddition::class, $result);
        $this->assertDatabaseHas('non_capital_additions', [
            'id' => $nonCapitalAddition->id,
            'company_id' => $nonCapitalAddition->company_id,
            'code' => $nonCapitalAdditionArr['code'],
            'name' => $nonCapitalAdditionArr['name'],
        ]);
    }

    public function test_non_capital_addition_actions_call_update_with_empty_array_parameters_expect_exception()
    {
        $this->expectException(Exception::class);

        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(NonCapitalAddition::factory())
            )->create();

        $nonCapitalAddition = $user->companies()->inRandomOrder()->first()
            ->nonCapitalAdditions()->inRandomOrder()->first();

        $nonCapitalAdditionArr = [];

        $this->nonCapitalAdditionActions->update($nonCapitalAddition, $nonCapitalAdditionArr);
    }
}
