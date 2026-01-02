<?php

namespace App\Rules;

use App\Actions\Branch\BranchActions;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BranchStoreValidCode implements ValidationRule
{
    protected $companyId;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== config('dcslab.KEYWORDS.AUTO')) {
            $branchActions = new BranchActions();

            if (! $branchActions->isUniqueCode($this->companyId, $value, null)) {
                $fail('rules.unique_code')->translate();

                return;
            }
        }
    }
}
