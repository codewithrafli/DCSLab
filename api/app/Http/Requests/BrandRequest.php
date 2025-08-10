<?php

namespace App\Http\Requests;

use App\Enums\RecordStatusEnum;
use App\Helpers\HashidsHelper;
use App\Models\Brand;
use App\Rules\BrandStoreValidCode;
use App\Rules\BrandUpdateValidCode;
use App\Rules\IsValidCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BrandRequest extends FormRequest
{
    public function authorize()
    {
        if (! Auth::check()) {
            return false;
        }

        /** @var \App\User */
        $user = Auth::user();
        $brand = $this->route('brand');

        $currentRouteMethod = $this->route()->getActionMethod();
        switch ($currentRouteMethod) {
            case 'readAny':
                return $user->can('viewAny', Brand::class) ? true : false;
            case 'read':
                return $user->can('view', Brand::class, $brand) ? true : false;
            case 'store':
                return $user->can('create', Brand::class) ? true : false;
            case 'update':
                return $user->can('update', Brand::class, $brand) ? true : false;
            case 'delete':
                return $user->can('delete', Brand::class, $brand) ? true : false;
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
                    'company_id' => ['required', 'numeric', 'bail', new IsValidCompany()],
                    'status' => ['nullable', 'numeric', 'in:'.implode(',', RecordStatusEnum::toArrayValue())],

                    'paginate' => ['required', 'boolean'],
                    'page' => ['nullable', 'required_if:paginate,true', 'numeric', 'min:1'],
                    'per_page' => ['nullable', 'required_if:paginate,true', 'numeric', 'min:10'],
                    'limit' => ['nullable', 'numeric', 'min:1'],
                ];
            case 'read':
                return [];
            case 'store':
                return [
                    'company_id' => ['required', 'numeric', 'bail', new IsValidCompany()],
                    'code' => ['required', 'string', 'max:255', new BrandStoreValidCode($this->company_id)],
                    'name' => ['required', 'string', 'max:255'],
                ];
            case 'update':
                return [
                    'company_id' => ['required', 'numeric', 'bail', new IsValidCompany()],
                    'code' => ['required', 'string', 'max:255', new BrandUpdateValidCode($this->company_id, $this->route('brand'))],
                    'name' => ['required', 'string', 'max:255'],
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
            'company_id' => trans('validation_attributes.brand.company'),
            'code' => trans('validation_attributes.brand.code'),
            'name' => trans('validation_attributes.brand.name'),
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
                ]);
                break;
            default:
                $this->merge([]);
                break;
        }
    }
}
