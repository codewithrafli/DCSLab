<?php

namespace App\Rules;

use App\Actions\PurchaseOrderDownPayments\PurchaseOrderDownPaymentsActions;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PurchaseOrderDownPaymentsUpdateValidCode implements ValidationRule
{
    protected $companyId;

    protected $purchaseOrderDownPayments;

    public function __construct($companyId, $purchaseOrderDownPayments)
    {
        $this->companyId = $companyId;
        $this->purchaseOrderDownPayments = $purchaseOrderDownPayments;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== config('dcslab.KEYWORDS.AUTO')) {
            $purchaseOrderDownPaymentsActions = new PurchaseOrderDownPaymentsActions();

            if (! $purchaseOrderDownPaymentsActions->isUniqueCode($this->companyId, $value, $this->purchaseOrderDownPayments->id)) {
                $fail('rules.unique_code')->translate();

                return;
            }
        }
    }
}
