<?php

namespace Tests\Unit\Actions\NonCapitalAdditionActions;

use App\Actions\NonCapitalAddition\NonCapitalAdditionActions;
use App\Models\Company;
use App\Models\NonCapitalAddition;
use App\Models\User;
use Exception;
use Tests\ActionsTestCase;

class NonCapitalAdditionActionsCreateTest extends ActionsTestCase
{
    private NonCapitalAdditionActions $nonCapitalAdditionActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->nonCapitalAdditionActions = new NonCapitalAdditionActions();
    }

    public function test_non_capital_addition_actions_call_create_expect_db_has_record()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $company = $user->companies()->inRandomOrder()->first();

        $nonCapitalAdditionArr = NonCapitalAddition::factory()->for($company)
            ->make()->toArray();

        $result = $this->nonCapitalAdditionActions->create($nonCapitalAdditionArr);

        $this->assertDatabaseHas('non_capital_additions', [
            'id' => $result->id,
            'company_id' => $nonCapitalAdditionArr['company_id'],
            'code' => $nonCapitalAdditionArr['code'],
            'name' => $nonCapitalAdditionArr['name'],
        ]);
    }

    public function test_non_capital_addition_actions_call_create_with_empty_array_parameters_expect_exception()
    {
        $this->expectException(Exception::class);
        $this->nonCapitalAdditionActions->create([]);
    }
}
