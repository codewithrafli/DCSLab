<?php

namespace Tests\Unit\Actions\PurchaseOrderDownPaymentsActions;

use App\Actions\PurchaseOrderDownPayments\PurchaseOrderDownPaymentsActions;
use App\Models\Company;
use App\Models\PurchaseOrderDownPayments;
use App\Models\User;
use Exception;
use Tests\ActionsTestCase;

class PurchaseOrderDownPaymentsActionsCreateTest extends ActionsTestCase
{
    private PurchaseOrderDownPaymentsActions $purchaseOrderDownPaymentsActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->purchaseOrderDownPaymentsActions = new PurchaseOrderDownPaymentsActions();
    }

    public function test_purchase_order_down_payments_actions_call_create_expect_db_has_record()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault())
            ->create();

        $company = $user->companies()->inRandomOrder()->first();

        $purchaseOrderDownPaymentsArr = PurchaseOrderDownPayments::factory()->for($company)
            ->make()->toArray();

        $result = $this->purchaseOrderDownPaymentsActions->create($purchaseOrderDownPaymentsArr);

        $this->assertDatabaseHas('purchase_order_down_payments', [
            'id' => $result->id,
            'company_id' => $purchaseOrderDownPaymentsArr['company_id'],
            'code' => $purchaseOrderDownPaymentsArr['code'],
            'name' => $purchaseOrderDownPaymentsArr['name'],
        ]);
    }

    public function test_purchase_order_down_payments_actions_call_create_with_empty_array_parameters_expect_exception()
    {
        $this->expectException(Exception::class);
        $this->purchaseOrderDownPaymentsActions->create([]);
    }
}
