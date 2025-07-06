<?php

namespace App\Http\Requests;

use App\Enums\RecordStatusEnum;
use App\Models\Company;
use App\Rules\CompanyStoreValidCode;
use App\Rules\CompanyStoreValidDefault;
use App\Rules\CompanyStoreValidStatus;
use App\Rules\CompanyUpdateValidCode;
use App\Rules\CompanyUpdateValidDefault;
use App\Rules\CompanyUpdateValidStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class CompanyRequest extends FormRequest
{
    protected $user;

    public function authorize()
    {
        if (! Auth::check()) {
            return false;
        }

        /** @var \App\User */
        $this->user = Auth::user();
        $company = $this->route('company');

        $currentRouteMethod = $this->route()->getActionMethod();
        switch ($currentRouteMethod) {
            case 'readAny':
                return $this->user->can('viewAny', Company::class) ? true : false;
            case 'read':
                return $this->user->can('view', Company::class, $company) ? true : false;
            case 'store':
                return $this->user->can('create', Company::class) ? true : false;
            case 'update':
                return $this->user->can('update', Company::class, $company) ? true : false;
            case 'delete':
                return $this->user->can('delete', Company::class, $company) ? true : false;
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
                    'with' => ['nullable', 'array'],
                    'with_trashed' => ['nullable', 'boolean'],

                    'search' => ['nullable', 'string'],
                    'default' => ['nullable', 'boolean'],
                    'status' => ['nullable', new Enum(RecordStatusEnum::class)],

                    'paginate' => ['required', 'boolean'],
                    'page' => ['required_if:paginate,true', 'numeric', 'min:1'],
                    'per_page' => ['required_if:paginate,true', 'numeric', 'min:10'],
                    'limit' => ['nullable', 'integer', 'min:10'],
                ];
            case 'read':
                return [];
            case 'store':
                return [
                    /* Test Validation Error For Code */
                    //'code' => ['required', 'max:1', 'alpha'],
                    //'name' => ['required', 'max:1'],
                    /* Test Validation Error For Code */
                    'code' => ['required', 'string', 'max:255', new CompanyStoreValidCode($this->user)],
                    'name' => ['required', 'string', 'max:255'],
                    'address' => ['nullable', 'string', 'max:255'],
                    'default' => ['required', 'boolean', new CompanyStoreValidDefault($this->user)],
                    'status' => ['required', new Enum(RecordStatusEnum::class), new CompanyStoreValidStatus($this->input('default'))],
                ];
            case 'update':
                return [
                    'code' => ['required', 'string', 'max:255', new CompanyUpdateValidCode($this->user, $this->route('company'))],
                    'name' => ['required', 'string', 'max:255'],
                    'address' => ['nullable', 'string', 'max:255'],
                    'default' => ['required', 'boolean', new CompanyUpdateValidDefault($this->user)],
                    'status' => ['required', new Enum(RecordStatusEnum::class), new CompanyUpdateValidStatus($this->input('default'))],
                ];
            case 'delete':
                return [];
            default:
                return [
                    '' => 'required',
                ];
        }
    }

    public function attributes()
    {
        return [
            'company_id' => trans('validation_attributes.company.company'),
            'code' => trans('validation_attributes.company.code'),
            'name' => trans('validation_attributes.company.name'),
            'address' => trans('validation_attributes.company.address'),
            'default' => trans('validation_attributes.company.default'),
            'status' => trans('validation_attributes.company.status'),
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
                    'with' => $this->has('with') ? $this->with : [],
                    'with_trashed' => $this->has('with_trashed') ? $this->with_trashed : null,

                    'search' => $this->has('search') ? $this->search : null,
                    'default' => $this->has('default') ? $this->default : null,
                    'status' => RecordStatusEnum::isValid($this->status) ? RecordStatusEnum::resolveToEnum($this->status)->value : null,

                    'page' => $this->has('page') ? $this->page : null,
                    'per_page' => $this->has('per_page') ? $this->per_page : null,
                    'limit' => $this->has('limit') ? $this->limit : null,
                ]);
                break;
            case 'read':
                $this->merge([]);
                break;
            case 'store':
                $this->merge([
                    'address' => $this->has('address') ? $this['address'] : null,
                    'status' => RecordStatusEnum::isValid($this->status) ? RecordStatusEnum::resolveToEnum($this->status)->value : null,
                ]);
                break;
            case 'update':
                $this->merge([
                    'address' => $this->has('address') ? $this['address'] : null,
                    'status' => RecordStatusEnum::isValid($this->status) ? RecordStatusEnum::resolveToEnum($this->status)->value : null,
                ]);
                break;
            default:
                $this->merge([]);
                break;
        }
    }
}
