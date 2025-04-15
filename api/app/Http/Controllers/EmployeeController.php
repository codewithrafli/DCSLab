<?php

namespace App\Http\Controllers;

use App\Actions\Employee\EmployeeActions;
use App\Http\Requests\EmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Exception;

class EmployeeController extends BaseController
{
    private $employeeActions;

    public function __construct(EmployeeActions $employeeActions)
    {
        parent::__construct();

        $this->employeeActions = $employeeActions;
    }

    public function store(EmployeeRequest $employeeRequest)
    {
        $request = $employeeRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->employeeActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(EmployeeRequest $employeeRequest)
    {
        $request = $employeeRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->employeeActions->readAny(
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
            $response = EmployeeResource::collection($result);

            return $response;
        }
    }

    public function read(Employee $employee, EmployeeRequest $employeeRequest)
    {
        $request = $employeeRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->employeeActions->read($employee);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new EmployeeResource($result);

            return $response;
        }
    }

    public function update(Employee $employee, EmployeeRequest $employeeRequest)
    {
        $request = $employeeRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->employeeActions->update(
                employee: $employee,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(Employee $employee, EmployeeRequest $employeeRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->employeeActions->delete($employee);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
