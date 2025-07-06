<?php

namespace App\Http\Requests;

use App\Enums\PaymentTermType;
use App\Enums\RecordStatusEnum;
use App\Helpers\HashidsHelper;
use App\Models\Customer;
use App\Rules\CustomerStoreValidCode;
use App\Rules\CustomerStoreValidGroup;
use App\Rules\CustomerUpdateValidCode;
use App\Rules\CustomerUpdateValidGroup;
use App\Rules\IsValidCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class CustomerRequest extends FormRequest
{
    public function authorize()
    {
        if (! Auth::check()) {
            return false;
        }

        /** @var \App\User */
        $user = Auth::user();
        $customer = $this->route('customer');

        $currentRouteMethod = $this->route()->getActionMethod();
        switch ($currentRouteMethod) {
            case 'readAny':
                return $user->can('viewAny', Customer::class) ? true : false;
            case 'read':
                return $user->can('view', Customer::class, $customer) ? true : false;
            case 'store':
                return $user->can('create', Customer::class) ? true : false;
            case 'update':
                return $user->can('update', Customer::class, $customer) ? true : false;
            case 'delete':
                return $user->can('delete', Customer::class, $customer) ? true : false;
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
                    'status' => ['nullable', 'integer', 'in:'.implode(',', RecordStatusEnum::toArrayValue())],

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
                    'code' => ['required', 'string', 'max:255', new CustomerStoreValidCode($this->company_id)],
                    'is_member' => ['required', 'boolean'],
                    'name' => ['required', 'string', 'max:255'],
                    'group_id' => ['nullable', 'integer', 'bail', new CustomerStoreValidGroup($this->company_id)],
                    'zone' => ['required', 'string', 'max:255'],
                    'max_open_invoice' => ['required', 'integer', 'min:0'],
                    'max_outstanding_invoice' => ['required', 'numeric', 'min:0'],
                    'max_invoice_age' => ['required', 'integer', 'min:0'],
                    'payment_term_type' => ['required', new Enum(PaymentTermType::class)],
                    'payment_term' => ['required', 'integer', 'min:0'],
                    'taxable_enterprise' => ['required', 'boolean'],
                    'tax_id' => ['nullable', 'string', 'max:255'],
                    'status' => ['required', new Enum(RecordStatusEnum::class)],
                    'remarks' => ['nullable', 'string', 'max:255'],
                ];
            case 'update':
                return [
                    'company_id' => ['required', 'integer', 'bail', new IsValidCompany()],
                    'code' => ['required', 'string', 'max:255', new CustomerUpdateValidCode($this->company_id, $this->route('customer'))],
                    'is_member' => ['required', 'boolean'],
                    'name' => ['required', 'string', 'max:255'],
                    'group_id' => ['nullable', 'integer', new CustomerUpdateValidGroup($this->company_id, $this->route('customer'))],
                    'zone' => ['required', 'string', 'max:255'],
                    'max_open_invoice' => ['required', 'integer', 'min:0'],
                    'max_outstanding_invoice' => ['required', 'numeric', 'min:0'],
                    'max_invoice_age' => ['required', 'integer', 'min:0'],
                    'payment_term_type' => ['required', new Enum(PaymentTermType::class)],
                    'payment_term' => ['required', 'integer', 'min:0'],
                    'taxable_enterprise' => ['required', 'boolean'],
                    'tax_id' => ['required', 'string', 'max:255'],
                    'status' => ['required', new Enum(RecordStatusEnum::class)],
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
            'company_id' => trans('validation_attributes.customer.company'),
            'code' => trans('validation_attributes.customer.code'),
            'is_member' => trans('validation_attributes.customer.is_member'),
            'name' => trans('validation_attributes.customer.name'),
            'zone' => trans('validation_attributes.customer.zone'),
            'max_open_invoice' => trans('validation_attributes.customer.max_open_invoice'),
            'max_outstanding_invoice' => trans('validation_attributes.customer.max_outstanding_invoice'),
            'max_invoice_age' => trans('validation_attributes.customer.max_invoice_age'),
            'payment_term_type' => trans('validation_attributes.customer.payment_term_type'),
            'payment_term' => trans('validation_attributes.customer.payment_term'),
            'taxable_enterprise' => trans('validation_attributes.customer.taxable_enterprise'),
            'tax_id' => trans('validation_attributes.customer.tax_id'),
            'status' => trans('validation_attributes.customer.status'),
            'remarks' => trans('validation_attributes.customer.remarks'),
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
                    'group_id' => $this->has('group_id') && $this->group_id ? HashidsHelper::decodeId($this->group_id) : null,
                    'payment_term_type' => PaymentTermType::isValid($this->payment_term_type) ? PaymentTermType::resolveToEnum($this->payment_term_type)->value : null,
                    'status' => RecordStatusEnum::isValid($this->status) ? RecordStatusEnum::resolveToEnum($this->status)->value : null,
                    'remarks' => $this->has('remarks') ? $this['remarks'] : null,
                ]);

                break;
            default:
                $this->merge([]);

                break;
        }
    }
}
