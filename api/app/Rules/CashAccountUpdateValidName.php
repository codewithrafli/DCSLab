<?php

namespace App\Rules;

use App\Models\CashAccount;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CashAccountUpdateValidName implements ValidationRule
{
    protected $companyId;

    protected $cashAccount;

    public function __construct($companyId, $cashAccount)
    {
        $this->companyId = $companyId;
        $this->cashAccount = $cashAccount;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $data = CashAccount::whereCompanyId($this->companyId)->where('name', $value);

        if ($data->exists() && $this->cashAccount->name !== $value) {
            $fail('rules.unique_name')->translate();

            return;
        }
    }
}
