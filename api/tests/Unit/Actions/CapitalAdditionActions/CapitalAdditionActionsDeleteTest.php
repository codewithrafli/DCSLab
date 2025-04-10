<?php

namespace Tests\Unit\Actions\CapitalAdditionActions;

use App\Actions\CapitalAddition\CapitalAdditionActions;
use App\Models\CapitalAddition;
use App\Models\Company;
use App\Models\User;
use Tests\ActionsTestCase;

class CapitalAdditionActionsDeleteTest extends ActionsTestCase
{
    private CapitalAdditionActions $capitalAdditionActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->capitalAdditionActions = new CapitalAdditionActions();
    }

    public function test_capital_addition_actions_call_delete_expect_bool()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(CapitalAddition::factory())
            )->create();

        $capitalAddition = $user->companies()->inRandomOrder()->first()
            ->capitalAdditions()->inRandomOrder()->first();
        $result = $this->capitalAdditionActions->delete($capitalAddition);

        $this->assertIsBool($result);
        $this->assertTrue($result);
        $this->assertSoftDeleted('capital_additions', [
            'id' => $capitalAddition->id,
        ]);
    }
}
