<?php

namespace App\Rules;

use App\Actions\Company\CompanyActions;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CompanyStoreValidCode implements ValidationRule
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== config('dcslab.KEYWORDS.AUTO')) {
            $companyActions = new CompanyActions();

            if (! $companyActions->isUniqueCode($this->user, $value, null)) {
                $fail('rules.unique_code')->translate();

                return;
            }
        }
    }
}
