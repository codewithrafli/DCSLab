<?php

namespace Tests\Unit\Actions\PurchaseOrderDownPaymentsActions;

use App\Actions\PurchaseOrderDownPayments\PurchaseOrderDownPaymentsActions;
use App\Models\Company;
use App\Models\PurchaseOrderDownPayments;
use App\Models\User;
use Tests\ActionsTestCase;

class PurchaseOrderDownPaymentsActionsDeleteTest extends ActionsTestCase
{
    private PurchaseOrderDownPaymentsActions $purchaseOrderDownPaymentsActions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->purchaseOrderDownPaymentsActions = new PurchaseOrderDownPaymentsActions();
    }

    public function test_purchase_order_down_payments_actions_call_delete_expect_bool()
    {
        $user = User::factory()
            ->has(Company::factory()->setStatusActive()->setIsDefault()
                ->has(PurchaseOrderDownPayments::factory())
            )->create();

        $purchaseOrderDownPayments = $user->companies()->inRandomOrder()->first()
            ->purchaseOrderDownPayments()->inRandomOrder()->first();
        $result = $this->purchaseOrderDownPaymentsActions->delete($purchaseOrderDownPayments);

        $this->assertIsBool($result);
        $this->assertTrue($result);
        $this->assertSoftDeleted('purchase_order_down_payments', [
            'id' => $purchaseOrderDownPayments->id,
        ]);
    }
}
