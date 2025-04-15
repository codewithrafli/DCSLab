<?php

namespace App\Rules;

use App\Models\CashAccount;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CashAccountStoreValidName implements ValidationRule
{
    protected $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cashAccount = CashAccount::whereCompanyId($this->companyId)->where('name', $value);

        if ($cashAccount->exists()) {
            $fail('rules.unique_name')->translate();

            return;
        }
    }
}
