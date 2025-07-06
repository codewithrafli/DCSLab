<?php

namespace Tests\Feature\API\ProductCategoryAPI;

use App\Enums\UserRolesEnum;
use App\Models\Company;
use App\Models\ProductCategory;
use App\Models\Role;
use App\Models\User;
use Tests\APITestCase;
use Vinkla\Hashids\Facades\Hashids;

class ProductCategoryAPIEditTest extends APITestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_product_category_api_call_update_without_authorization_expect_unauthorized_message()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $company = $user->companies()->inRandomOrder()->first();
        $productCategory = ProductCategory::factory()->for($company)->create();

        $productCategoryArr = ProductCategory::factory()->make([
            'company_id' => Hashids::encode($company->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.product.product_category.edit', $productCategory->ulid), $productCategoryArr);

        $api->assertStatus(401);
    }

    public function test_product_category_api_call_update_without_access_right_expect_unauthorized_message()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $this->actingAs($user);

        $company = $user->companies()->inRandomOrder()->first();
        $productCategory = ProductCategory::factory()->for($company)->create();

        $productCategoryArr = ProductCategory::factory()->make([
            'company_id' => Hashids::encode($company->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.product.product_category.edit', $productCategory->ulid), $productCategoryArr);

        $api->assertStatus(403);
    }

    public function test_product_category_api_call_update_with_script_tags_in_payload_expect_stripped()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function test_product_category_api_call_update_with_script_tags_in_payload_expect_encoded()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function test_product_category_api_call_update_expect_successful()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $this->actingAs($user);

        $company = $user->companies()->inRandomOrder()->first();
        $productCategory = ProductCategory::factory()->for($company)->create();

        $productCategoryArr = ProductCategory::factory()->make([
            'company_id' => Hashids::encode($company->id),
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.product.product_category.edit', $productCategory->ulid), $productCategoryArr);

        $api->assertSuccessful();
        $this->assertDatabaseHas('product_categories', [
            'id' => $productCategory->id,
            'company_id' => $company->id,
            'code' => $productCategoryArr['code'],
            'name' => $productCategoryArr['name'],
            'type' => $productCategoryArr['type'],
        ]);
    }

    public function test_product_category_api_call_update_with_nonexistance_branch_id_expect_failed()
    {
        $this->markTestSkipped('Nothing to test yet.');
    }

    public function test_product_category_api_call_update_and_use_existing_code_in_same_company_expect_failed()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $this->actingAs($user);

        $company = $user->companies->first();
        ProductCategory::factory()->for($company)->count(2)->create();

        $productCategories = $company->productCategories()->inRandomOrder()->take(2)->get();
        $productCategory_1 = $productCategories[0];
        $productCategory_2 = $productCategories[1];

        $productCategoryArr = ProductCategory::factory()->make([
            'company_id' => Hashids::encode($company->id),
            'code' => $productCategory_1->code,
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.product.product_category.edit', $productCategory_2->ulid), $productCategoryArr);

        $api->assertStatus(422);
        $api->assertJsonStructure([
            'errors',
        ]);
    }

    public function test_product_category_api_call_update_and_use_existing_code_in_different_company_expect_successful()
    {
        $user = User::factory()
            ->hasAttached(Role::where('name', '=', UserRolesEnum::DEVELOPER->value)->first())
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->has(Company::factory()->setStatusActive())
            ->create();

        $this->actingAs($user);

        $companies = $user->companies()->inRandomOrder()->get();

        $company_1 = $companies[0];
        ProductCategory::factory()->for($company_1)->create([
            'code' => 'test1',
        ]);

        $company_2 = $companies[1];
        $productCategory_2 = ProductCategory::factory()->for($company_2)->create([
            'code' => 'test2',
        ]);

        $productCategoryArr = ProductCategory::factory()->make([
            'company_id' => Hashids::encode($company_2->id),
            'code' => 'test1',
        ])->toArray();

        $api = $this->json('POST', route('api.post.db.product.product_category.edit', $productCategory_2->ulid), $productCategoryArr);

        $api->assertSuccessful();
    }
}
