<?php

namespace App\Http\Requests;

use App\Enums\ProductTypeEnum;
use App\Enums\RecordStatusEnum;
use App\Helpers\HashidsHelper;
use App\Models\Product;
use App\Rules\IsValidBrand;
use App\Rules\IsValidCompany;
use App\Rules\IsValidProductCategory;
use App\Rules\ProductStoreValidCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Vinkla\Hashids\Facades\Hashids;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        if (! Auth::check()) {
            return false;
        }

        /** @var \App\User */
        $user = Auth::user();
        $product = $this->route('product');

        $currentRouteMethod = $this->route()->getActionMethod();
        switch ($currentRouteMethod) {
            case 'readAny':
                return $user->can('viewAny', Product::class) ? true : false;
            case 'read':
                return $user->can('view', Product::class, $product) ? true : false;
            case 'store':
                return $user->can('create', Product::class) ? true : false;
            case 'update':
                return $user->can('update', Product::class, $product) ? true : false;
            case 'delete':
                return $user->can('delete', Product::class, $product) ? true : false;
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
                    'code' => ['required', 'string', 'max:255', new ProductStoreValidCode($this->company_id)],
                    'category_id' => ['required', 'integer', new IsValidProductCategory()],
                    'brand_id' => ['required', 'integer', new IsValidBrand()],
                    'name' => ['required', 'string', 'max:255'],
                    'slug' => ['required', 'string', 'max:255'],
                    'is_manufacturer_sku' => ['required', 'boolean'],
                    'is_taxable' => ['required', 'boolean'],
                    'vat_rate' => ['required', 'numeric'],
                    'is_price_include_vat' => ['required', 'boolean'],
                    'is_use_serial_number' => ['required', 'boolean'],
                    'is_expirable' => ['required', 'boolean'],
                    'point' => ['required', 'integer'],
                    'remarks' => ['nullable', 'string', 'max:255'],
                    'type' => ['required', 'integer', 'in:'.implode(',', ProductTypeEnum::toArrayValue())],
                    'status' => ['required', 'integer', 'in:'.implode(',', RecordStatusEnum::toArrayValue())],

                    'product_units' => 'required|array',
                    'product_units.*.unit_id' => 'required|integer',
                    'product_units.*.code' => 'required|string',
                    'product_units.*.is_base' => 'required|boolean',
                    'product_units.*.conversion_value' => 'required|numeric',
                    'product_units.*.is_primary_unit' => 'required|boolean',
                    'product_units.*.remarks' => 'nullable|string',
                ];
            case 'update':
                return [
                    'company_id' => ['required', 'integer', 'bail', new IsValidCompany()],
                    'code' => ['required', 'string', 'max:255', new ProductStoreValidCode($this->company_id)],
                    'category_id' => ['required', 'integer', new IsValidProductCategory()],
                    'brand_id' => ['required', 'integer', new IsValidBrand()],
                    'name' => ['required', 'string', 'max:255'],
                    'slug' => ['required', 'string', 'max:255'],
                    'is_manufacturer_sku' => ['required', 'boolean'],
                    'is_taxable' => ['required', 'boolean'],
                    'vat_rate' => ['required', 'numeric'],
                    'is_price_include_vat' => ['required', 'boolean'],
                    'is_use_serial_number' => ['required', 'boolean'],
                    'is_expirable' => ['required', 'boolean'],
                    'point' => ['required', 'integer'],
                    'remarks' => ['nullable', 'string', 'max:255'],
                    'type' => ['required', 'integer', 'in:'.implode(',', ProductTypeEnum::toArrayValue())],
                    'status' => ['required', 'integer', 'in:'.implode(',', RecordStatusEnum::toArrayValue())],

                    'delete_product_unit_ids' => 'nullable|array',
                    'delete_product_unit_ids.*' => 'required|integer|exists:product_units,id',

                    'product_units' => 'required|array',
                    'product_units.*.unit_id' => 'required|integer',
                    'product_units.*.code' => 'required|string',
                    'product_units.*.is_base' => 'required|boolean',
                    'product_units.*.conversion_value' => 'required|numeric',
                    'product_units.*.is_primary_unit' => 'required|boolean',
                    'product_units.*.remarks' => 'nullable|string',
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
            'company_id' => trans('validation_attributes.product.company'),
            'code' => trans('validation_attributes.product.code'),
            'is_manufacturer_sku' => trans('validation_attributes.product.is_manufacturer_sku'),
            'category_id' => trans('validation_attributes.product.product_category'),
            'brand_id' => trans('validation_attributes.product.brand'),
            'name' => trans('validation_attributes.product.name'),
            'slug' => trans('validation_attributes.product.slug'),
            'is_taxable' => trans('validation_attributes.product.is_taxable'),
            'vat_rate' => trans('validation_attributes.product.vat_rate'),
            'is_price_include_vat' => trans('validation_attributes.product.is_price_include_vat'),
            'is_use_serial_number' => trans('validation_attributes.product.is_use_serial_number'),
            'is_expirable' => trans('validation_attributes.product.is_expirable'),
            'point' => trans('validation_attributes.product.point'),
            'remarks' => trans('validation_attributes.product.remarks'),
            'type' => trans('validation_attributes.product.product_type'),
            'status' => trans('validation_attributes.product.status'),
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
                $brand_id = null;
                if ($this->has('brand_id')) {
                    if ($this->brand_id) {
                        $brand_id = Hashids::decode($this['brand_id'])[0];
                    }
                }
                $product_category_id = null;
                if ($this->has('product_category_id')) {
                    if ($this->product_category_id) {
                        $product_category_id = Hashids::decode($this['product_category_id'])[0];
                    }
                }

                $this->merge([
                    'company_id' => $this->has('company_id') ? Hashids::decode($this['company_id'])[0] : '',
                    'product_category_id' => $product_category_id,
                    'brand_id' => $brand_id,
                ]);
                break;
            default:
                $this->merge([]);
                break;
        }
    }
}
