<?php

namespace App\Http\Requests;

use App\Enums\RecordStatus;
use App\Helpers\HashidsHelper;
use App\Models\Investor;
use App\Rules\InvestorStoreValidCode;
use App\Rules\InvestorStoreValidName;
use App\Rules\InvestorUpdateValidCode;
use App\Rules\InvestorUpdateValidName;
use App\Rules\IsValidCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InvestorRequest extends FormRequest
{
    public function authorize()
    {
        if (! Auth::check()) {
            return false;
        }

        /** @var \App\User */
        $user = Auth::user();
        $investor = $this->route('investor');

        $currentRouteMethod = $this->route()->getActionMethod();
        switch ($currentRouteMethod) {
            case 'readAny':
                return $user->can('viewAny', Investor::class) ? true : false;
            case 'read':
                return $user->can('view', Investor::class, $investor) ? true : false;
            case 'store':
                return $user->can('create', Investor::class) ? true : false;
            case 'update':
                return $user->can('update', Investor::class, $investor) ? true : false;
            case 'delete':
                return $user->can('delete', Investor::class, $investor) ? true : false;
            default:
                return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $currentRouteMethod = $this->route()->getActionMethod();
        switch ($currentRouteMethod) {
            case 'readAny':
                return [
                    'refresh' => ['required', 'boolean'],
                    'with_trashed' => ['required', 'boolean'],

                    'search' => ['nullable', 'string'],
                    'company_id' => ['required', 'integer', 'bail', new IsValidCompany()],
                    'status' => ['nullable', 'integer', 'in:'.implode(',', RecordStatus::toArrayValue())],

                    'paginate' => ['required', 'boolean'],
                    'page' => ['nullable', 'required_if:paginate,true', 'numeric', 'min:1'],
                    'per_page' => ['nullable', 'required_if:paginate,true', 'numeric', 'min:10'],
                    'limit' => ['nullable', 'integer', 'min:1'],
                ];
            case 'read':
                return [];
            case 'store':
                return [
                    'company_id' => ['required', 'integer', 'bail', new IsValidCompany()],
                    'code' => ['required', 'string', 'max:255', new InvestorStoreValidCode($this->company_id)],
                    'name' => ['required', 'string', 'max:255', new InvestorStoreValidName($this->company_id)],
                    'remarks' => ['nullable', 'string', 'max:255'],
                ];
            case 'update':
                return [
                    'company_id' => ['required', 'integer', 'bail', new IsValidCompany()],
                    'code' => ['required', 'string', 'max:255', new InvestorUpdateValidCode($this->company_id, $this->route('investor'))],
                    'name' => ['required', 'string', 'max:255', new InvestorUpdateValidName($this->company_id, $this->route('investor'))],
                    'remarks' => ['nullable', 'string', 'max:255'],
                ];
            case 'delete':
                return [

                ];
            default:
                return [
                    '' => 'required',
                ];
        }
    }

    public function attributes()
    {
        return [
            'company_id' => trans('validation_attributes.investor.company'),
            'code' => trans('validation_attributes.investor.code'),
            'name' => trans('validation_attributes.investor.name'),
            'remarks' => trans('validation_attributes.investor.remarks'),
        ];
    }

    public function validationData()
    {
        $additionalArray = [];

        return array_merge($this->all(), $additionalArray);
    }

    public function prepareForValidation()
    {
        $currentRouteMethod = $this->route()->getActionMethod();
        switch ($currentRouteMethod) {
            case 'readAny':
                $this->merge([
                    'with_trashed' => $this->has('with_trashed') ? $this->with_trashed : null,

                    'search' => $this->has('search') ? $this->search : null,
                    'company_id' => $this->has('company_id') ? HashidsHelper::decodeId($this->company_id) : null,

                    'page' => $this->has('page') ? $this->page : null,
                    'per_page' => $this->has('per_page') ? $this->per_page : null,
                    'limit' => $this->has('limit') ? $this->limit : null,
                ]);
                break;
            case 'read':
                $this->merge([]);
                break;
            case 'store':
            case 'update':
                $this->merge([
                    'company_id' => $this->has('company_id') ? HashidsHelper::decodeId($this->company_id) : null,
                    'remarks' => $this->has('remarks') ? $this['remarks'] : null,
                ]);
                break;
            default:
                $this->merge([]);
                break;
        }
    }
}
