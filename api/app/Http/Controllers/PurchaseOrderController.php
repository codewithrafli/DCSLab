<?php

namespace App\Http\Controllers;

use App\Actions\PurchaseOrder\PurchaseOrderActions;
use App\Http\Requests\PurchaseOrderRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use Exception;

class PurchaseOrderController extends BaseController
{
    private $purchaseOrderActions;

    public function __construct(PurchaseOrderActions $purchaseOrderActions)
    {
        parent::__construct();

        $this->purchaseOrderActions = $purchaseOrderActions;
    }

    public function store(PurchaseOrderRequest $purchaseOrderRequest)
    {
        $request = $purchaseOrderRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseOrderActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(PurchaseOrderRequest $purchaseOrderRequest)
    {
        $request = $purchaseOrderRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseOrderActions->readAny(
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
            $response = PurchaseOrderResource::collection($result);

            return $response;
        }
    }

    public function read(PurchaseOrder $purchaseOrder, PurchaseOrderRequest $purchaseOrderRequest)
    {
        $request = $purchaseOrderRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseOrderActions->read($purchaseOrder);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new PurchaseOrderResource($result);

            return $response;
        }
    }

    public function update(PurchaseOrder $purchaseOrder, PurchaseOrderRequest $purchaseOrderRequest)
    {
        $request = $purchaseOrderRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseOrderActions->update(
                purchaseOrder: $purchaseOrder,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(PurchaseOrder $purchaseOrder, PurchaseOrderRequest $purchaseOrderRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->purchaseOrderActions->delete($purchaseOrder);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
