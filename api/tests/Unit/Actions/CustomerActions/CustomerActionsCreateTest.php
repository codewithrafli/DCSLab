<?php

namespace Tests\Unit\Actions\CustomerActions;

use App\Actions\Customer\CustomerActions;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Exception;
use Tests\ActionsTestCase;

class CustomerActionsCreateTest extends ActionsTestCase
{
    private CustomerActions $customerActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerActions = new CustomerActions();
    }

    public function test_customer_actions_call_create_expect_db_has_record()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $company = $user->companies()->inRandomOrder()->first();

        $customerArr = Customer::factory()->for($company)
            ->make()->toArray();

        $result = $this->customerActions->create($customerArr);

        $this->assertDatabaseHas('customers', [
            'id' => $result->id,
            'company_id' => $customerArr['company_id'],
            'code' => $customerArr['code'],
            'name' => $customerArr['name'],
        ]);
    }

    public function test_customer_actions_call_create_with_empty_array_parameters_expect_exception()
    {
        $this->expectException(Exception::class);
        $this->customerActions->create([]);
    }
}
