<?php

namespace Tests\Unit\Actions\PurchaseOrderDownPaymentsActions;

use App\Actions\PurchaseOrderDownPayments\PurchaseOrderDownPaymentsActions;
use App\Models\Company;
use App\Models\PurchaseOrderDownPayments;
use App\Models\User;
use Exception;
use Tests\ActionsTestCase;

class PurchaseOrderDownPaymentsActionsEditTest extends ActionsTestCase
{
    private PurchaseOrderDownPaymentsActions $purchaseOrderDownPaymentsActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->purchaseOrderDownPaymentsActions = new PurchaseOrderDownPaymentsActions();
    }

    public function test_purchase_order_down_payments_actions_call_update_expect_db_updated()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(PurchaseOrderDownPayments::factory())
            )->create();

        $company = $user->companies()->inRandomOrder()->first();
        $purchaseOrderDownPayments = $company->purchaseOrderDownPayments()->inRandomOrder()->first();

        $purchaseOrderDownPaymentsArr = PurchaseOrderDownPayments::factory()->make()->toArray();

        $result = $this->purchaseOrderDownPaymentsActions->update($purchaseOrderDownPayments, $purchaseOrderDownPaymentsArr);

        $this->assertInstanceOf(PurchaseOrderDownPayments::class, $result);
        $this->assertDatabaseHas('purchase_order_down_payments', [
            'id' => $purchaseOrderDownPayments->id,
            'company_id' => $purchaseOrderDownPayments->company_id,
            'code' => $purchaseOrderDownPaymentsArr['code'],
            'name' => $purchaseOrderDownPaymentsArr['name'],
        ]);
    }

    public function test_purchase_order_down_payments_actions_call_update_with_empty_array_parameters_expect_exception()
    {
        $this->expectException(Exception::class);

        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(PurchaseOrderDownPayments::factory())
            )->create();

        $purchaseOrderDownPayments = $user->companies()->inRandomOrder()->first()
            ->purchaseOrderDownPayments()->inRandomOrder()->first();

        $purchaseOrderDownPaymentsArr = [];

        $this->purchaseOrderDownPaymentsActions->update($purchaseOrderDownPayments, $purchaseOrderDownPaymentsArr);
    }
}
