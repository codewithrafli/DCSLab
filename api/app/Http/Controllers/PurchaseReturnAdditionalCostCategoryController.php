<?php

namespace App\Http\Controllers;

use App\Actions\PurchaseReturnAdditionalCostCategory\PurchaseReturnAdditionalCostCategoryActions;
use App\Http\Requests\PurchaseReturnAdditionalCostCategoryRequest;
use App\Http\Resources\PurchaseReturnAdditionalCostCategoryResource;
use App\Models\PurchaseReturnAdditionalCostCategory;
use Exception;

class PurchaseReturnAdditionalCostCategoryController extends BaseController
{
    private $purchaseReturnAdditionalCostCategoryActions;

    public function __construct(PurchaseReturnAdditionalCostCategoryActions $purchaseReturnAdditionalCostCategoryActions)
    {
        parent::__construct();

        $this->purchaseReturnAdditionalCostCategoryActions = $purchaseReturnAdditionalCostCategoryActions;
    }

    public function store(PurchaseReturnAdditionalCostCategoryRequest $purchaseReturnAdditionalCostCategoryRequest)
    {
        $request = $purchaseReturnAdditionalCostCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseReturnAdditionalCostCategoryActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(PurchaseReturnAdditionalCostCategoryRequest $purchaseReturnAdditionalCostCategoryRequest)
    {
        $request = $purchaseReturnAdditionalCostCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseReturnAdditionalCostCategoryActions->readAny(
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
            $response = PurchaseReturnAdditionalCostCategoryResource::collection($result);

            return $response;
        }
    }

    public function read(PurchaseReturnAdditionalCostCategory $purchaseReturnAdditionalCostCategory, PurchaseReturnAdditionalCostCategoryRequest $purchaseReturnAdditionalCostCategoryRequest)
    {
        $request = $purchaseReturnAdditionalCostCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseReturnAdditionalCostCategoryActions->read($purchaseReturnAdditionalCostCategory);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new PurchaseReturnAdditionalCostCategoryResource($result);

            return $response;
        }
    }

    public function update(PurchaseReturnAdditionalCostCategory $purchaseReturnAdditionalCostCategory, PurchaseReturnAdditionalCostCategoryRequest $purchaseReturnAdditionalCostCategoryRequest)
    {
        $request = $purchaseReturnAdditionalCostCategoryRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseReturnAdditionalCostCategoryActions->update(
                purchaseReturnAdditionalCostCategory: $purchaseReturnAdditionalCostCategory,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(PurchaseReturnAdditionalCostCategory $purchaseReturnAdditionalCostCategory, PurchaseReturnAdditionalCostCategoryRequest $purchaseReturnAdditionalCostCategoryRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->purchaseReturnAdditionalCostCategoryActions->delete($purchaseReturnAdditionalCostCategory);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
