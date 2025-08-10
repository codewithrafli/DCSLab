<?php

namespace App\Http\Requests;

use App\Enums\RecordStatusEnum;
use App\Helpers\HashidsHelper;
use App\Models\ProductUnit;
use App\Rules\IsValidCompany;
use App\Rules\IsValidProduct;
use App\Rules\IsValidUnit;
use App\Rules\ProductUnitStoreValidCode;
use App\Rules\ProductUnitUpdateValidCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductUnitRequest extends FormRequest
{
    public function authorize()
    {
        if (! Auth::check()) {
            return false;
        }

        /** @var \App\User */
        $user = Auth::user();
        $productUnit = $this->route('product_unit');

        $currentRouteMethod = $this->route()->getActionMethod();
        switch ($currentRouteMethod) {
            case 'readAny':
                return $user->can('viewAny', ProductUnit::class) ? true : false;
            case 'read':
                return $user->can('view', ProductUnit::class, $productUnit) ? true : false;
            case 'store':
                return $user->can('create', ProductUnit::class) ? true : false;
            case 'update':
                return $user->can('update', ProductUnit::class, $productUnit) ? true : false;
            case 'delete':
                return $user->can('delete', ProductUnit::class, $productUnit) ? true : false;
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
                    'product_id' => ['required', 'integer', 'bail', new IsValidProduct($this->company_id)],
                    'unit_id' => ['required', 'integer', 'bail', new IsValidUnit($this->company_id)],
                    'code' => ['required', 'string', 'max:255', new ProductUnitStoreValidCode($this->company_id)],
                    'is_base' => ['required', 'boolean'],
                    'conversion_value' => ['required', 'numeric', 'min:0'],
                    'is_primary_unit' => ['required', 'boolean'],
                    'remarks' => ['nullable', 'string', 'max:255'],
                ];
            case 'update':
                return [
                    'company_id' => ['required', 'integer', 'bail', new IsValidCompany()],
                    'product_id' => ['required', 'integer', 'bail', new IsValidProduct($this->company_id)],
                    'unit_id' => ['required', 'integer', 'bail', new IsValidUnit($this->company_id)],
                    'code' => ['required', 'string', 'max:255', new ProductUnitUpdateValidCode($this->company_id, $this->route('product_unit'))],
                    'is_base' => ['required', 'boolean'],
                    'conversion_value' => ['required', 'numeric', 'min:0'],
                    'is_primary_unit' => ['required', 'boolean'],
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
            'company_id' => trans('validation_attributes.product_unit.company'),
            'product_id' => trans('validation_attributes.product_unit.product'),
            'unit_id' => trans('validation_attributes.product_unit.unit'),
            'code' => trans('validation_attributes.product_unit.code'),
            'is_base' => trans('validation_attributes.product_unit.is_base'),
            'conversion_value' => trans('validation_attributes.product_unit.conversion_value'),
            'is_primary_unit' => trans('validation_attributes.product_unit.is_primary_unit'),
            'remarks' => trans('validation_attributes.product_unit.remarks'),
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
