<?php

namespace Tests\Unit\Actions\CustomerActions;

use App\Actions\Customer\CustomerActions;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Exception;
use Tests\ActionsTestCase;

class CustomerActionsEditTest extends ActionsTestCase
{
    private CustomerActions $customerActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerActions = new CustomerActions();
    }

    public function test_customer_actions_call_update_expect_db_updated()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(Customer::factory())
            )->create();

        $company = $user->companies()->inRandomOrder()->first();
        $customer = $company->customers()->inRandomOrder()->first();

        $customerArr = Customer::factory()->make()->toArray();

        $result = $this->customerActions->update($customer, $customerArr);

        $this->assertInstanceOf(Customer::class, $result);
        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'company_id' => $customer->company_id,
            'code' => $customerArr['code'],
            'name' => $customerArr['name'],
        ]);
    }

    public function test_customer_actions_call_update_with_empty_array_parameters_expect_exception()
    {
        $this->expectException(Exception::class);

        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(Customer::factory())
            )->create();

        $customer = $user->companies()->inRandomOrder()->first()
            ->customers()->inRandomOrder()->first();

        $customerArr = [];

        $this->customerActions->update($customer, $customerArr);
    }
}
