<?php

namespace App\Http\Controllers;

use App\Actions\CapitalAddition\CapitalAdditionActions;
use App\Http\Requests\CapitalAdditionRequest;
use App\Http\Resources\CapitalAdditionResource;
use App\Models\CapitalAddition;
use Exception;

class CapitalAdditionController extends BaseController
{
    private $capitalAdditionActions;

    public function __construct(CapitalAdditionActions $capitalAdditionActions)
    {
        parent::__construct();

        $this->capitalAdditionActions = $capitalAdditionActions;
    }

    public function store(CapitalAdditionRequest $capitalAdditionRequest)
    {
        $request = $capitalAdditionRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->capitalAdditionActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(CapitalAdditionRequest $capitalAdditionRequest)
    {
        $request = $capitalAdditionRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->capitalAdditionActions->readAny(
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
            $response = CapitalAdditionResource::collection($result);

            return $response;
        }
    }

    public function read(CapitalAddition $capitalAddition, CapitalAdditionRequest $capitalAdditionRequest)
    {
        $request = $capitalAdditionRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->capitalAdditionActions->read($capitalAddition);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new CapitalAdditionResource($result);

            return $response;
        }
    }

    public function update(CapitalAddition $capitalAddition, CapitalAdditionRequest $capitalAdditionRequest)
    {
        $request = $capitalAdditionRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->capitalAdditionActions->update(
                capitalAddition: $capitalAddition,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(CapitalAddition $capitalAddition, CapitalAdditionRequest $capitalAdditionRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->capitalAdditionActions->delete($capitalAddition);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
