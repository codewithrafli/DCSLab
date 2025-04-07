<?php

namespace App\Http\Controllers;

use App\Actions\PurchaseAdditionalCostCategory\PurchaseAdditionalCostCategoryActions;
use App\Http\Requests\PurchaseAdditionalCostCategoryRequest;
use App\Http\Resources\PurchaseAdditionalCostCategoryResource;
use App\Models\PurchaseAdditionalCostCategory;
use Exception;

class PurchaseAdditionalCostCategoryController extends BaseController
{
    private $purchaseAdditionalCostCategoryActions;

    public function __construct(PurchaseAdditionalCostCategoryActions $purchaseAdditionalCostCategoryActions)
    {
        parent::__construct();

        $this->purchaseAdditionalCostCategoryActions = $purchaseAdditionalCostCategoryActions;
    }

    public function store(PurchaseAdditionalCostCategoryRequest $purchaseAdditionalCostCategoryRequest)
    {
        $request = $purchaseAdditionalCostCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseAdditionalCostCategoryActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(PurchaseAdditionalCostCategoryRequest $purchaseAdditionalCostCategoryRequest)
    {
        $request = $purchaseAdditionalCostCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseAdditionalCostCategoryActions->readAny(
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
            $response = PurchaseAdditionalCostCategoryResource::collection($result);

            return $response;
        }
    }

    public function read(PurchaseAdditionalCostCategory $purchaseAdditionalCostCategory, PurchaseAdditionalCostCategoryRequest $purchaseAdditionalCostCategoryRequest)
    {
        $request = $purchaseAdditionalCostCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseAdditionalCostCategoryActions->read($purchaseAdditionalCostCategory);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new PurchaseAdditionalCostCategoryResource($result);

            return $response;
        }
    }

    public function update(PurchaseAdditionalCostCategory $purchaseAdditionalCostCategory, PurchaseAdditionalCostCategoryRequest $purchaseAdditionalCostCategoryRequest)
    {
        $request = $purchaseAdditionalCostCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseAdditionalCostCategoryActions->update(
                purchaseAdditionalCostCategory: $purchaseAdditionalCostCategory,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(PurchaseAdditionalCostCategory $purchaseAdditionalCostCategory, PurchaseAdditionalCostCategoryRequest $purchaseAdditionalCostCategoryRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->purchaseAdditionalCostCategoryActions->delete($purchaseAdditionalCostCategory);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
