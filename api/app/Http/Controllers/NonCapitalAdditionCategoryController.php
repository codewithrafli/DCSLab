<?php

namespace App\Http\Controllers;

use App\Actions\NonCapitalAdditionCategory\NonCapitalAdditionCategoryActions;
use App\Http\Requests\NonCapitalAdditionCategoryRequest;
use App\Http\Resources\NonCapitalAdditionCategoryResource;
use App\Models\NonCapitalAdditionCategory;
use Exception;

class NonCapitalAdditionCategoryController extends BaseController
{
    private $nonCapitalAdditionCategoryActions;

    public function __construct(NonCapitalAdditionCategoryActions $nonCapitalAdditionCategoryActions)
    {
        parent::__construct();

        $this->nonCapitalAdditionCategoryActions = $nonCapitalAdditionCategoryActions;
    }

    public function store(NonCapitalAdditionCategoryRequest $nonCapitalAdditionCategoryRequest)
    {
        $request = $nonCapitalAdditionCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalAdditionCategoryActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(NonCapitalAdditionCategoryRequest $nonCapitalAdditionCategoryRequest)
    {
        $request = $nonCapitalAdditionCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalAdditionCategoryActions->readAny(
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
            $response = NonCapitalAdditionCategoryResource::collection($result);

            return $response;
        }
    }

    public function read(NonCapitalAdditionCategory $nonCapitalAdditionCategory, NonCapitalAdditionCategoryRequest $nonCapitalAdditionCategoryRequest)
    {
        $request = $nonCapitalAdditionCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalAdditionCategoryActions->read($nonCapitalAdditionCategory);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new NonCapitalAdditionCategoryResource($result);

            return $response;
        }
    }

    public function update(NonCapitalAdditionCategory $nonCapitalAdditionCategory, NonCapitalAdditionCategoryRequest $nonCapitalAdditionCategoryRequest)
    {
        $request = $nonCapitalAdditionCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalAdditionCategoryActions->update(
                nonCapitalAdditionCategory: $nonCapitalAdditionCategory,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(NonCapitalAdditionCategory $nonCapitalAdditionCategory, NonCapitalAdditionCategoryRequest $nonCapitalAdditionCategoryRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalAdditionCategoryActions->delete($nonCapitalAdditionCategory);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
