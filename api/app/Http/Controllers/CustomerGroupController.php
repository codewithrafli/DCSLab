<?php

namespace App\Http\Controllers;

use App\Actions\CustomerGroup\CustomerGroupActions;
use App\Http\Requests\CustomerGroupRequest;
use App\Http\Resources\CustomerGroupResource;
use App\Models\CustomerGroup;
use Exception;

class CustomerGroupController extends BaseController
{
    private $customerGroupActions;

    public function __construct(CustomerGroupActions $customerGroupActions)
    {
        parent::__construct();

        $this->customerGroupActions = $customerGroupActions;
    }

    public function store(CustomerGroupRequest $customerGroupRequest)
    {
        $request = $customerGroupRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->customerGroupActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(CustomerGroupRequest $customerGroupRequest)
    {
        $request = $customerGroupRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->customerGroupActions->readAny(
                useCache: $request['refresh'],
                withTrashed: $request['with_trashed'],

                search: $request['search'],
                companyId: $request['company_id'],

                paginate: $request['paginate'],
                page: $request['page'],
                perPage: $request['per_page'],
                limit: $request['limit'],
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = CustomerGroupResource::collection($result);

            return $response;
        }
    }

    public function read(CustomerGroup $customerGroup, CustomerGroupRequest $customerGroupRequest)
    {
        $request = $customerGroupRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->customerGroupActions->read($customerGroup);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new CustomerGroupResource($result);

            return $response;
        }
    }

    public function update(CustomerGroup $customerGroup, CustomerGroupRequest $customerGroupRequest)
    {
        $request = $customerGroupRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->customerGroupActions->update(
                customerGroup: $customerGroup,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(CustomerGroup $customerGroup, CustomerGroupRequest $customerGroupRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->customerGroupActions->delete($customerGroup);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
