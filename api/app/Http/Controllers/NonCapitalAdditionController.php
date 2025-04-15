<?php

namespace App\Http\Controllers;

use App\Actions\NonCapitalAddition\NonCapitalAdditionActions;
use App\Http\Requests\NonCapitalAdditionRequest;
use App\Http\Resources\NonCapitalAdditionResource;
use App\Models\NonCapitalAddition;
use Exception;

class NonCapitalAdditionController extends BaseController
{
    private $nonCapitalAdditionActions;

    public function __construct(NonCapitalAdditionActions $nonCapitalAdditionActions)
    {
        parent::__construct();

        $this->nonCapitalAdditionActions = $nonCapitalAdditionActions;
    }

    public function store(NonCapitalAdditionRequest $nonCapitalAdditionRequest)
    {
        $request = $nonCapitalAdditionRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalAdditionActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(NonCapitalAdditionRequest $nonCapitalAdditionRequest)
    {
        $request = $nonCapitalAdditionRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalAdditionActions->readAny(
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
            $response = NonCapitalAdditionResource::collection($result);

            return $response;
        }
    }

    public function read(NonCapitalAddition $nonCapitalAddition, NonCapitalAdditionRequest $nonCapitalAdditionRequest)
    {
        $request = $nonCapitalAdditionRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalAdditionActions->read($nonCapitalAddition);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new NonCapitalAdditionResource($result);

            return $response;
        }
    }

    public function update(NonCapitalAddition $nonCapitalAddition, NonCapitalAdditionRequest $nonCapitalAdditionRequest)
    {
        $request = $nonCapitalAdditionRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalAdditionActions->update(
                nonCapitalAddition: $nonCapitalAddition,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(NonCapitalAddition $nonCapitalAddition, NonCapitalAdditionRequest $nonCapitalAdditionRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalAdditionActions->delete($nonCapitalAddition);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
