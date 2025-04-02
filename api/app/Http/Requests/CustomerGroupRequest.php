<?php

namespace App\Http\Requests;

use App\Enums\PaymentTermType;
use App\Enums\RecordStatus;
use App\Enums\RoundOn;
use App\Helpers\HashidsHelper;
use App\Models\CustomerGroup;
use App\Rules\CustomerGroupStoreValidCode;
use App\Rules\CustomerGroupUpdateValidCode;
use App\Rules\IsValidCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CustomerGroupRequest extends FormRequest
{
    public function authorize()
    {
        if (! Auth::check()) {
            return false;
        }

        /** @var \App\User */
        $user = Auth::user();
        $customerGroup = $this->route('customer_group');

        $currentRouteMethod = $this->route()->getActionMethod();
        switch ($currentRouteMethod) {
            case 'readAny':
                return $user->can('viewAny', CustomerGroup::class) ? true : false;
            case 'read':
                return $user->can('view', CustomerGroup::class, $customerGroup) ? true : false;
            case 'store':
                return $user->can('create', CustomerGroup::class) ? true : false;
            case 'update':
                return $user->can('update', CustomerGroup::class, $customerGroup) ? true : false;
            case 'delete':
                return $user->can('delete', CustomerGroup::class, $customerGroup) ? true : false;
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
                    'code' => ['required', 'string', 'max:255', new CustomerGroupStoreValidCode($this->company_id)],
                    'name' => ['required', 'string', 'max:255'],
                    'max_open_invoice' => ['required', 'integer', 'min:0'],
                    'max_outstanding_invoice' => ['required', 'integer', 'min:0'],
                    'max_invoice_age' => ['required', 'integer', 'min:0'],
                    'payment_term_type' => ['required', 'string', 'in:'.implode(',', PaymentTermType::toArrayValue())],
                    'payment_term' => ['required', 'integer', 'min:0'],
                    'selling_point' => ['required', 'integer', 'max:255'],
                    'selling_point_multiple' => ['required', 'integer', 'min:0'],
                    'sell_at_cost' => ['required', 'boolean'],
                    'price_markup_percent' => ['required', 'integer', 'min:0'],
                    'price_markup_nominal' => ['required', 'integer', 'min:0'],
                    'price_markdown_percent' => ['required', 'integer', 'min:0'],
                    'price_markdown_nominal' => ['required', 'integer', 'min:0'],
                    'round_on' => ['required', 'string', 'in:'.implode(',', RoundOn::toArrayValue())],
                    'round_digit' => ['required', 'integer', 'min:0'],
                    'remarks' => ['nullable', 'string', 'max:255'],
                ];
            case 'update':
                return [
                    'company_id' => ['required', 'integer', 'bail', new IsValidCompany()],
                    'code' => ['required', 'string', 'max:255', new CustomerGroupUpdateValidCode($this->company_id, $this->route('customer_group'))],
                    'name' => ['required', 'string', 'max:255'],
                    'max_open_invoice' => ['required', 'integer', 'min:0'],
                    'max_outstanding_invoice' => ['required', 'integer', 'min:0'],
                    'max_invoice_age' => ['required', 'integer', 'min:0'],
                    'payment_term_type' => ['required', 'string', 'in:'.implode(',', PaymentTermType::toArrayValue())],
                    'payment_term' => ['required', 'integer', 'min:0'],
                    'selling_point' => ['required', 'string', 'max:255'],
                    'selling_point_multiple' => ['required', 'integer', 'min:0'],
                    'sell_at_cost' => ['required', 'boolean'],
                    'price_markup_percent' => ['required', 'integer', 'min:0'],
                    'price_markup_nominal' => ['required', 'integer', 'min:0'],
                    'price_markdown_percent' => ['required', 'integer', 'min:0'],
                    'price_markdown_nominal' => ['required', 'integer', 'min:0'],
                    'round_on' => ['required', 'string', 'in:'.implode(',', RoundOn::toArrayValue())],
                    'round_digit' => ['required', 'integer', 'min:0'],
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
            'company_id' => trans('validation_attributes.customer_group.company'),
            'code' => trans('validation_attributes.customer_group.code'),
            'name' => trans('validation_attributes.customer_group.name'),
            'max_open_invoice' => trans('validation_attributes.customer_group.max_open_invoice'),
            'max_outstanding_invoice' => trans('validation_attributes.customer_group.max_outstanding_invoice'),
            'max_invoice_age' => trans('validation_attributes.customer_group.max_invoice_age'),
            'payment_term_type' => trans('validation_attributes.customer_group.payment_term_type'),
            'payment_term' => trans('validation_attributes.customer_group.payment_term'),
            'selling_point' => trans('validation_attributes.customer_group.selling_point'),
            'selling_point_multiple' => trans('validation_attributes.customer_group.selling_point_multiple'),
            'sell_at_cost' => trans('validation_attributes.customer_group.sell_at_cost'),
            'price_markup_percent' => trans('validation_attributes.customer_group.price_markup_percent'),
            'price_markup_nominal' => trans('validation_attributes.customer_group.price_markup_nominal'),
            'price_markdown_percent' => trans('validation_attributes.customer_group.price_markdown_percent'),
            'price_markdown_nominal' => trans('validation_attributes.customer_group.price_markdown_nominal'),
            'round_on' => trans('validation_attributes.customer_group.round_on'),
            'round_digit' => trans('validation_attributes.customer_group.round_digit'),
            'remarks' => trans('validation_attributes.customer_group.remarks'),
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
