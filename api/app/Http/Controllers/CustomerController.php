<?php

namespace App\Http\Controllers;

use App\Actions\Customer\CustomerActions;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Exception;

class CustomerController extends BaseController
{
    private $customerActions;

    public function __construct(CustomerActions $customerActions)
    {
        parent::__construct();

        $this->customerActions = $customerActions;
    }

    public function store(CustomerRequest $customerRequest)
    {
        $request = $customerRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->customerActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(CustomerRequest $customerRequest)
    {
        $request = $customerRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->customerActions->readAny(
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
            $response = CustomerResource::collection($result);

            return $response;
        }
    }

    public function read(Customer $customer, CustomerRequest $customerRequest)
    {
        $request = $customerRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->customerActions->read($customer);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new CustomerResource($result);

            return $response;
        }
    }

    public function update(Customer $customer, CustomerRequest $customerRequest)
    {
        $request = $customerRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->customerActions->update(
                customer: $customer,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(Customer $customer, CustomerRequest $customerRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->customerActions->delete($customer);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
