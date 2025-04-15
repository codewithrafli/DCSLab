<?php

namespace Tests\Unit\Actions\NonCapitalAdditionActions;

use App\Actions\NonCapitalAddition\NonCapitalAdditionActions;
use App\Models\Company;
use App\Models\NonCapitalAddition;
use App\Models\User;
use Tests\ActionsTestCase;

class NonCapitalAdditionActionsDeleteTest extends ActionsTestCase
{
    private NonCapitalAdditionActions $nonCapitalAdditionActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->nonCapitalAdditionActions = new NonCapitalAdditionActions();
    }

    public function test_non_capital_addition_actions_call_delete_expect_bool()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(NonCapitalAddition::factory())
            )->create();

        $nonCapitalAddition = $user->companies()->inRandomOrder()->first()
            ->nonCapitalAdditions()->inRandomOrder()->first();
        $result = $this->nonCapitalAdditionActions->delete($nonCapitalAddition);

        $this->assertIsBool($result);
        $this->assertTrue($result);
        $this->assertSoftDeleted('non_capital_additions', [
            'id' => $nonCapitalAddition->id,
        ]);
    }
}
