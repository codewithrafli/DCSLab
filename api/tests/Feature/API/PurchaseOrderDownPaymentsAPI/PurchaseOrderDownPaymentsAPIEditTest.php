<?php

namespace Tests\Feature\API\PurchaseOrderDownPaymentsAPI;

use App\Enums\UserRoles;
use App\Models\Company;
use App\Models\PurchaseOrderDownPayments;
use App\Models\Role;
use App\Models\User;
use Tests\APITestCase;
use Vinkla\Hashids\Facades\Hashids;

class PurchaseOrderDownPaymentsAPIEditTest extends APITestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_purchase_order_down_payments_api_call_update_without_authorization_expect_unauthorized_message()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRoles::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $company = $user->companies()->inRandomOrder()->first();
        $purchaseOrderDownPayments = PurchaseOrderDownPayments::factory()->for($company)->create();

        $purchaseOrderDownPaymentsArr = PurchaseOrderDownPayments::factory()->make([
            'company_id' => Hashids::encode($company->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.product.purchase_order_down_payments.edit', $purchaseOrderDownPayments->ulid), $purchaseOrderDownPaymentsArr);

        $api->assertStatus(401);
    }

    public function test_purchase_order_down_payments_api_call_update_without_access_right_expect_unauthorized_message()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $this->actingAs($user);

        $company = $user->companies()->inRandomOrder()->first();
        $purchaseOrderDownPayments = PurchaseOrderDownPayments::factory()->for($company)->create();

        $purchaseOrderDownPaymentsArr = PurchaseOrderDownPayments::factory()->make([
            'company_id' => Hashids::encode($company->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.product.purchase_order_down_payments.edit', $purchaseOrderDownPayments->ulid), $purchaseOrderDownPaymentsArr);

        $api->assertStatus(403);
    }

    public function test_purchase_order_down_payments_api_call_update_with_script_tags_in_payload_expect_stripped()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function test_purchase_order_down_payments_api_call_update_with_script_tags_in_payload_expect_encoded()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function test_purchase_order_down_payments_api_call_update_expect_successful()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRoles::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $this->actingAs($user);

        $company = $user->companies()->inRandomOrder()->first();
        $purchaseOrderDownPayments = PurchaseOrderDownPayments::factory()->for($company)->create();

        $purchaseOrderDownPaymentsArr = PurchaseOrderDownPayments::factory()->make([
            'company_id' => Hashids::encode($company->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.product.purchase_order_down_payments.edit', $purchaseOrderDownPayments->ulid), $purchaseOrderDownPaymentsArr);

        $api->assertSuccessful();
        $this->assertDatabaseHas('purchase_order_down_payments', [
            'id' => $purchaseOrderDownPayments->id,
            'company_id' => $company->id,
            'code' => $purchaseOrderDownPaymentsArr['code'],
            'name' => $purchaseOrderDownPaymentsArr['name'],
        ]);
    }

    public function test_purchase_order_down_payments_api_call_update_with_nonexistance_branch_id_expect_failed()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function test_purchase_order_down_payments_api_call_update_and_use_existing_code_in_same_company_expect_failed()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRoles::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $this->actingAs($user);

        $company = $user->companies->first();
        PurchaseOrderDownPayments::factory()->for($company)->count(2)->create();

        $purchaseOrderDownPayments = $company->purchaseOrderDownPayments()->inRandomOrder()->take(2)->get();
        $purchaseOrderDownPayments_1 = $purchaseOrderDownPayments[0];
        $purchaseOrderDownPayments_2 = $purchaseOrderDownPayments[1];

        $purchaseOrderDownPaymentsArr = PurchaseOrderDownPayments::factory()->make([
            'company_id' => Hashids::encode($company->id),
            'code' => $purchaseOrderDownPayments_1->code,
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.product.purchase_order_down_payments.edit', $purchaseOrderDownPayments_2->ulid), $purchaseOrderDownPaymentsArr);

        $api->assertStatus(422);
        $api->assertJsonStructure([
            'errors',
        ]);
    }

    public function test_purchase_order_down_payments_api_call_update_and_use_existing_code_in_different_company_expect_successful()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRoles::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->has(Company::factory()->setStatusActive())
            ->create();

        $this->actingAs($user);

        $companies = $user->companies()->inRandomOrder()->get();

        $company_1 = $companies[0];
        PurchaseOrderDownPayments::factory()->for($company_1)->create([
            'code' => 'test1',
        ]);

        $company_2 = $companies[1];
        $purchaseOrderDownPayments_2 = PurchaseOrderDownPayments::factory()->for($company_2)->create([
            'code' => 'test2',
        ]);

        $purchaseOrderDownPaymentsArr = PurchaseOrderDownPayments::factory()->make([
            'company_id' => Hashids::encode($company_2->id),
            'code' => 'test1',
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.product.purchase_order_down_payments.edit', $purchaseOrderDownPayments_2->ulid), $purchaseOrderDownPaymentsArr);

        $api->assertSuccessful();
    }
}
