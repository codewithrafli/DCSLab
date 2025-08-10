<?php

namespace Tests\Feature\API\CapitalAdditionAPI;

use App\Enums\UserRolesEnum;
use App\Models\CapitalAddition;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Tests\APITestCase;
use Vinkla\Hashids\Facades\Hashids;

class CapitalAdditionAPIEditTest extends APITestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_capital_addition_api_call_update_without_authorization_expect_unauthorized_message()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $company = $user->companies()->inRandomOrder()->first();
        $capitalAddition = CapitalAddition::factory()->for($company)->create();

        $capitalAdditionArr = CapitalAddition::factory()->make([
            'company_id' => Hashids::encode($company->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.capital.capital_addition.edit', $capitalAddition->ulid), $capitalAdditionArr);

        $api->assertStatus(401);
    }

    public function test_capital_addition_api_call_update_without_access_right_expect_unauthorized_message()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $this->actingAs($user);

        $company = $user->companies()->inRandomOrder()->first();
        $capitalAddition = CapitalAddition::factory()->for($company)->create();

        $capitalAdditionArr = CapitalAddition::factory()->make([
            'company_id' => Hashids::encode($company->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.capital.capital_addition.edit', $capitalAddition->ulid), $capitalAdditionArr);

        $api->assertStatus(403);
    }

    public function test_capital_addition_api_call_update_with_script_tags_in_payload_expect_stripped()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function test_capital_addition_api_call_update_with_script_tags_in_payload_expect_encoded()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function test_capital_addition_api_call_update_expect_successful()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $this->actingAs($user);

        $company = $user->companies()->inRandomOrder()->first();
        $capitalAddition = CapitalAddition::factory()->for($company)->create();

        $capitalAdditionArr = CapitalAddition::factory()->make([
            'company_id' => Hashids::encode($company->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.capital.capital_addition.edit', $capitalAddition->ulid), $capitalAdditionArr);

        $api->assertSuccessful();
        $this->assertDatabaseHas('capital_additions', [
            'id' => $capitalAddition->id,
            'company_id' => $company->id,
            'code' => $capitalAdditionArr['code'],
            'name' => $capitalAdditionArr['name'],
        ]);
    }

    public function test_capital_addition_api_call_update_with_nonexistance_branch_id_expect_failed()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function test_capital_addition_api_call_update_and_use_existing_code_in_same_company_expect_failed()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $this->actingAs($user);

        $company = $user->companies->first();
        CapitalAddition::factory()->for($company)->count(2)->create();

        $capitalAdditions = $company->capitalAdditions()->inRandomOrder()->take(2)->get();
        $capitalAddition_1 = $capitalAdditions[0];
        $capitalAddition_2 = $capitalAdditions[1];

        $capitalAdditionArr = CapitalAddition::factory()->make([
            'company_id' => Hashids::encode($company->id),
            'code' => $capitalAddition_1->code,
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.capital.capital_addition.edit', $capitalAddition_2->ulid), $capitalAdditionArr);

        $api->assertStatus(422);
        $api->assertJsonStructure([
            'errors',
        ]);
    }

    public function test_capital_addition_api_call_update_and_use_existing_code_in_different_company_expect_successful()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->has(Company::factory()->setStatusActive())
            ->create();

        $this->actingAs($user);

        $companies = $user->companies()->inRandomOrder()->get();

        $company_1 = $companies[0];
        CapitalAddition::factory()->for($company_1)->create([
            'code' => 'test1',
        ]);

        $company_2 = $companies[1];
        $capitalAddition_2 = CapitalAddition::factory()->for($company_2)->create([
            'code' => 'test2',
        ]);

        $capitalAdditionArr = CapitalAddition::factory()->make([
            'company_id' => Hashids::encode($company_2->id),
            'code' => 'test1',
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.capital.capital_addition.edit', $capitalAddition_2->ulid), $capitalAdditionArr);

        $api->assertSuccessful();
    }
}
