<?php

namespace Tests\Feature\API\CashAccountAPI;

use App\Enums\UserRolesEnum;
use App\Models\Branch;
use App\Models\CashAccount;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Tests\APITestCase;
use Vinkla\Hashids\Facades\Hashids;

class CashAccountAPICreateTest extends APITestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_cash_account_api_call_store_without_authorization_expect_unauthorized_message()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault()->has(Branch::factory()))
            ->create();

        $company = $user->companies()->whereHas('branches')->inRandomOrder()->first();

        $cashAccountArr = CashAccount::factory()->make([
            'company_id' => Hashids::encode($company->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.cash_account.save'), $cashAccountArr);

        $api->assertUnauthorized();
    }

    public function test_cash_account_api_call_store_without_access_right_expect_unauthorized_message()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()->has(Branch::factory()))
            ->create();

        $this->actingAs($user);

        $company = $user->companies()->whereHas('branches')->inRandomOrder()->first();

        $cashAccountArr = CashAccount::factory()->make([
            'company_id' => Hashids::encode($company->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.cash_account.save'), $cashAccountArr);

        $api->assertForbidden();
    }

    public function test_cash_account_api_call_store_with_script_tags_in_payload_expect_stripped()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function test_cash_account_api_call_store_with_script_tags_in_payload_expect_encoded()
    {
        $this->markTestSkipped('Test under construction');
    }

    public function test_cash_account_api_call_store_expect_successful()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault()->has(Branch::factory()))
            ->create();

        $this->actingAs($user);

        $company = $user->companies()->whereHas('branches')->inRandomOrder()->first();
        $branch = $company->branches()->inRandomOrder()->first();

        $cashAccountArr = CashAccount::factory()->make([
            'company_id' => Hashids::encode($company->id),
            'branch_id' => Hashids::encode($branch->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.cash_account.save'), $cashAccountArr);

        $api->assertSuccessful();
        $this->assertDatabaseHas('cash_accounts', [
            'company_id' => $company->id,
            'code' => $cashAccountArr['code'],
            'name' => $cashAccountArr['name'],
        ]);
    }

    public function test_cash_account_api_call_store_with_nonexistance_branch_id_expect_failed()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function test_cash_account_api_call_store_with_existing_code_in_same_company_expect_failed()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(
                Company::factory()->setStatusActive()->setIsDefault()->has(Branch::factory())
            )->create();

        $this->actingAs($user);

        $company = $user->companies()->inRandomOrder()->first();
        $branch = $company->branches()->inRandomOrder()->first();

        CashAccount::factory()->for($company)->create([
            'code' => 'test1',
            'branch_id' => $branch->id,
        ]);

        $cashAccountArr = CashAccount::factory()->make([
            'company_id' => Hashids::encode($company->id),
            'branch_id' => Hashids::encode($branch->id),
            'code' => 'test1',
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.cash_account.save'), $cashAccountArr);

        $api->assertStatus(422);
        $api->assertJsonStructure([
            'errors',
        ]);
    }

    public function test_cash_account_api_call_store_with_existing_code_in_different_company_expect_successful()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault()->has(Branch::factory()))
            ->has(Company::factory()->setStatusActive()->has(Branch::factory()))
            ->create();

        $this->actingAs($user);

        $companies = $user->companies()->whereHas('branches')->inRandomOrder()->take(2)->get();

        $company_1 = $companies[0];

        $company_2 = $companies[1];

        $branch_1 = $company_1->branches()->inRandomOrder()->first();

        CashAccount::factory()->for($company_1)->create([
            'code' => 'test1',
            'branch_id' => $branch_1->id,
        ]);

        $branch_2 = $company_2->branches()->inRandomOrder()->first();

        $cashAccountArr = CashAccount::factory()->make([
            'company_id' => Hashids::encode($company_2->id),
            'branch_id' => Hashids::encode($branch_2->id),
            'code' => 'test1',
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.cash_account.save'), $cashAccountArr);

        $api->assertSuccessful();
        $this->assertDatabaseHas('cash_accounts', [
            'company_id' => $company_2->id,
            'code' => $cashAccountArr['code'],
            'name' => $cashAccountArr['name'],
        ]);
    }

    public function test_cash_account_api_call_store_with_empty_string_parameters_expect_validation_error()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $this->actingAs($user);

        $cashAccountArr = [];

        $api = $this->json('POST', route('api.post.db.cash_account.save'), $cashAccountArr);

        $api->assertJsonValidationErrors(['company_id', 'branch_id', 'code', 'name', 'is_bank', 'is_active']);
    }
}
