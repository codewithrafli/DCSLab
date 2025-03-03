<?php

namespace App\Http\Requests;

use App\Enums\RecordStatus;
use App\Helpers\HashidsHelper;
use App\Models\Branch;
use App\Rules\BranchStoreValidCode;
use App\Rules\BranchStoreValidName;
use App\Rules\BranchStoreValidStatus;
use App\Rules\BranchUpdateValidCode;
use App\Rules\BranchUpdateValidIsMain;
use App\Rules\BranchUpdateValidName;
use App\Rules\BranchUpdateValidStatus;
use App\Rules\IsValidCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class BranchRequest extends FormRequest
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
        $branch = $this->route('branch');

        $currentRouteMethod = $this->route()->getActionMethod();
        switch ($currentRouteMethod) {
            case 'readAny':
                return $user->can('viewAny', Branch::class) ? true : false;
            case 'read':
                return $user->can('view', Branch::class, $branch) ? true : false;
            case 'store':
                return $user->can('create', Branch::class) ? true : false;
            case 'update':
                return $user->can('update', Branch::class, $branch) ? true : false;
            case 'delete':
                return $user->can('delete', Branch::class, $branch) ? true : false;
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
                    'company_id' => ['required', 'integer', 'bail', new IsValidCompany()],
                    'refresh' => ['nullable', 'boolean'],
                    'with' => ['nullable', 'array'],
                    'with_trashed' => ['nullable', 'boolean'],

                    'search' => ['nullable', 'string'],
                    'is_main' => ['nullable', 'boolean'],
                    'status' => ['nullable', 'integer', 'in:'.implode(',', RecordStatus::toArrayValue())],

                    'paginate' => ['required', 'boolean'],
                    'page' => ['required_if:paginate,true', 'numeric', 'min:1'],
                    'per_page' => ['required_if:paginate,true', 'numeric', 'min:10'],
                ];
            case 'read':
                return [];
            case 'store':
                return [
                    'company_id' => ['required', 'integer', 'bail', new IsValidCompany()],
                    'code' => ['required', 'string', 'max:255', new BranchStoreValidCode($this->input('company_id'))],
                    'name' => ['required', 'string', 'max:255', new BranchStoreValidName($this->input('company_id'))],
                    'address' => ['nullable', 'string', 'max:255'],
                    'city' => ['nullable', 'string', 'max:255'],
                    'contact' => ['nullable', 'string', 'max:255'],
                    'is_main' => ['required', 'boolean'],
                    'remarks' => ['nullable', 'string', 'max:255'],
                    'status' => ['required', new Enum(RecordStatus::class), 'bail', new BranchStoreValidStatus($this->input('is_main'))],
                ];
            case 'update':
                return [
                    'company_id' => ['required', 'integer', 'bail', new IsValidCompany()],
                    'code' => ['required', 'string', 'max:255', new BranchUpdateValidCode($this->input('company_id'), $this->route('branch'))],
                    'name' => ['required', 'string', 'max:255', new BranchUpdateValidName($this->input('company_id'), $this->route('branch'))],
                    'address' => ['nullable', 'string', 'max:255'],
                    'city' => ['nullable', 'string', 'max:255'],
                    'contact' => ['nullable', 'string', 'max:255'],
                    'is_main' => ['required', 'boolean', 'bail', new BranchUpdateValidIsMain($this->route('branch'))],
                    'remarks' => ['nullable', 'string', 'max:255'],
                    'status' => ['required', new Enum(RecordStatus::class), 'bail', new BranchUpdateValidStatus($this->route('is_main'))],
                ];
            case 'delete':
                $rules_delete = [];

                return $rules_delete;
            default:
                return [
                    '' => 'required',
                ];
        }
    }

    public function attributes()
    {
        return [
            'company_id' => trans('validation_attributes.branch.company'),
            'code' => trans('validation_attributes.branch.code'),
            'name' => trans('validation_attributes.branch.name'),
            'address' => trans('validation_attributes.branch.address'),
            'city' => trans('validation_attributes.branch.city'),
            'contact' => trans('validation_attributes.branch.contact'),
            'is_main' => trans('validation_attributes.branch.is_main'),
            'remarks' => trans('validation_attributes.branch.remarks'),
            'status' => trans('validation_attributes.branch.status'),
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
                    'company_id' => $this->has('company_id') ? HashidsHelper::decodeId($this->company_id) : null,
                    'refresh' => $this->has('refresh') ? $this->refresh : null,
                    'with' => $this->has('with') ? $this->with : [],
                    'with_trashed' => $this->has('with_trashed') ? $this->with_trashed : null,

                    'search' => $this->has('search') ? $this->search : null,
                    'is_main' => $this->has('is_main') ? $this->is_main : null,
                    'status' => RecordStatus::isValid($this->status) ? RecordStatus::resolveToEnum($this->status)->value : null,

                    'page' => $this->has('page') ? $this->page : null,
                    'per_page' => $this->has('per_page') ? $this->per_page : null,
                ]);
                break;
            case 'read':
                $this->merge([]);
                break;
            case 'store':
                $this->merge([
                    'company_id' => $this->has('company_id') ? HashidsHelper::decodeId($this->company_id) : null,
                    'status' => RecordStatus::isValid($this->status) ? RecordStatus::resolveToEnum($this->status)->value : null,
                ]);
                break;
            case 'update':
                $this->merge([
                    'company_id' => $this->has('company_id') ? HashidsHelper::decodeId($this->company_id) : null,
                    'address' => $this->has('address') ? $this->address : null,
                    'city' => $this->has('city') ? $this->city : null,
                    'contact' => $this->has('contact') ? $this->contact : null,
                    'remarks' => $this->has('remarks') ? $this->remarks : null,
                    'status' => RecordStatus::isValid($this->status) ? RecordStatus::resolveToEnum($this->status)->value : null,
                ]);
                break;
            default:
                $this->merge([]);
        }
    }
}
