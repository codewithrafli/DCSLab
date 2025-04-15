<?php

namespace App\Rules;

use App\Actions\CashAccount\CashAccountActions;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CashAccountUpdateValidCode implements ValidationRule
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
        if ($value !== config('dcslab.KEYWORDS.AUTO')) {
            $cashAccountActions = new CashAccountActions();

            if (! $cashAccountActions->isUniqueCode($this->companyId, $value, $this->cashAccount->id)) {
                $fail('rules.unique_code')->translate();

                return;
            }
        }
    }
}
