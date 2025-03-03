<?php

namespace App\Http\Requests;

use App\Enums\RecordStatus;
use App\Helpers\HashidsHelper;
use App\Models\Warehouse;
use App\Rules\IsValidBranch;
use App\Rules\IsValidCompany;
use App\Rules\WarehouseStoreValidCode;
use App\Rules\WarehouseStoreValidName;
use App\Rules\WarehouseStoreValidStatus;
use App\Rules\WarehouseUpdateValidCode;
use App\Rules\WarehouseUpdateValidName;
use App\Rules\WarehouseUpdateValidStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class WarehouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (! Auth::check()) {
            return false;
        }

        /** @var \App\User */
        $user = Auth::user();
        $warehouse = $this->route('warehouse');

        $currentRouteMethod = $this->route()->getActionMethod();
        switch ($currentRouteMethod) {
            case 'readAny':
                return $user->can('viewAny', Warehouse::class) ? true : false;
            case 'read':
                return $user->can('view', Warehouse::class, $warehouse) ? true : false;
            case 'store':
                return $user->can('create', Warehouse::class) ? true : false;
            case 'update':
                return $user->can('update', Warehouse::class, $warehouse) ? true : false;
            case 'delete':
                return $user->can('delete', Warehouse::class, $warehouse) ? true : false;
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
                    'branch_id' => ['nullable', 'integer', 'bail', new IsValidBranch($this->company_id, false)],
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
                    'branch_id' => ['required', 'integer', 'bail', new IsValidBranch($this->company_id, true)],
                    'code' => ['required', 'string', 'max:255', new WarehouseStoreValidCode($this->company_id)],
                    'name' => ['required', 'string', 'max:255', new WarehouseStoreValidName($this->company_id)],
                    'address' => ['nullable', 'string', 'max:255'],
                    'city' => ['nullable', 'string', 'max:255'],
                    'contact' => ['nullable', 'string', 'max:255'],
                    'remarks' => ['nullable', 'string', 'max:255'],
                    'status' => ['required', new Enum(RecordStatus::class), new WarehouseStoreValidStatus($this->input('default'))],

                ];
            case 'update':
                return [
                    'company_id' => ['required', 'integer', 'bail', new IsValidCompany()],
                    'branch_id' => ['required', 'integer', 'bail', new IsValidBranch($this->company_id, true)],
                    'code' => ['required', 'string', 'max:255', new WarehouseUpdateValidCode($this->company_id, $this->route('warehouse'))],
                    'name' => ['required', 'string', 'max:255', new WarehouseUpdateValidName($this->company_id, $this->route('warehouse'))],
                    'address' => ['nullable', 'string', 'max:255'],
                    'city' => ['nullable', 'string', 'max:255'],
                    'contact' => ['nullable', 'string', 'max:255'],
                    'remarks' => ['nullable', 'string', 'max:255'],
                    'status' => ['required', new Enum(RecordStatus::class), new WarehouseUpdateValidStatus($this->input('default'))],

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
            'company_id' => trans('validation_attributes.warehouse.company'),
            'branch_id' => trans('validation_attributes.warehouse.branch'),
            'code' => trans('validation_attributes.warehouse.code'),
            'name' => trans('validation_attributes.warehouse.name'),
            'address' => trans('validation_attributes.warehouse.address'),
            'city' => trans('validation_attributes.warehouse.city'),
            'contact' => trans('validation_attributes.warehouse.contact'),
            'remarks' => trans('validation_attributes.warehouse.remarks'),
            'status' => trans('validation_attributes.warehouse.status'),
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
                    'branch_id' => $this->has('branch_id') ? HashidsHelper::decodeId($this->branch_id) : null,
                    'status' => $this->has('status') ? $this->status : null,

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
                    'company_id' => $this->has('company_id') ? HashidsHelper::decodeId($this->company_id) : null,
                    'branch_id' => $this->has('branch_id') ? HashidsHelper::decodeId($this->branch_id) : null,
                    'address' => $this->has('address') ? $this['address'] : null,
                    'city' => $this->has('city') ? $this['city'] : null,
                    'contact' => $this->has('contact') ? $this['contact'] : null,
                    'remarks' => $this->has('remarks') ? $this['remarks'] : null,
                    'status' => RecordStatus::isValid($this->status) ? RecordStatus::resolveToEnum($this->status)->value : null,
                ]);
                break;
            case 'update':
                $this->merge([
                    'company_id' => $this->has('company_id') ? HashidsHelper::decodeId($this->company_id) : null,
                    'branch_id' => $this->has('branch_id') ? HashidsHelper::decodeId($this->branch_id) : null,
                    'address' => $this->has('address') ? $this['address'] : null,
                    'city' => $this->has('city') ? $this['city'] : null,
                    'contact' => $this->has('contact') ? $this['contact'] : null,
                    'remarks' => $this->has('remarks') ? $this['remarks'] : null,
                    'status' => RecordStatus::isValid($this->status) ? RecordStatus::resolveToEnum($this->status)->value : null,
                ]);
                break;
            default:
                $this->merge([]);
                break;
        }
    }
}
