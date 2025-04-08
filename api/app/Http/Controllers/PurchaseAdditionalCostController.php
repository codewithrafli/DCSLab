<?php

namespace App\Http\Controllers;

use App\Actions\PurchaseAdditionalCost\PurchaseAdditionalCostActions;
use App\Http\Requests\PurchaseAdditionalCostRequest;
use App\Http\Resources\PurchaseAdditionalCostResource;
use App\Models\PurchaseAdditionalCost;
use Exception;

class PurchaseAdditionalCostController extends BaseController
{
    private $purchaseAdditionalCostActions;

    public function __construct(PurchaseAdditionalCostActions $purchaseAdditionalCostActions)
    {
        parent::__construct();

        $this->purchaseAdditionalCostActions = $purchaseAdditionalCostActions;
    }

    public function store(PurchaseAdditionalCostRequest $purchaseAdditionalCostRequest)
    {
        $request = $purchaseAdditionalCostRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseAdditionalCostActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(PurchaseAdditionalCostRequest $purchaseAdditionalCostRequest)
    {
        $request = $purchaseAdditionalCostRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseAdditionalCostActions->readAny(
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
            $response = PurchaseAdditionalCostResource::collection($result);

            return $response;
        }
    }

    public function read(PurchaseAdditionalCost $purchaseAdditionalCost, PurchaseAdditionalCostRequest $purchaseAdditionalCostRequest)
    {
        $request = $purchaseAdditionalCostRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseAdditionalCostActions->read($purchaseAdditionalCost);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new PurchaseAdditionalCostResource($result);

            return $response;
        }
    }

    public function update(PurchaseAdditionalCost $purchaseAdditionalCost, PurchaseAdditionalCostRequest $purchaseAdditionalCostRequest)
    {
        $request = $purchaseAdditionalCostRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseAdditionalCostActions->update(
                purchaseAdditionalCost: $purchaseAdditionalCost,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(PurchaseAdditionalCost $purchaseAdditionalCost, PurchaseAdditionalCostRequest $purchaseAdditionalCostRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->purchaseAdditionalCostActions->delete($purchaseAdditionalCost);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
