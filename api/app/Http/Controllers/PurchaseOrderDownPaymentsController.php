<?php

namespace App\Http\Controllers;

use App\Actions\PurchaseOrderDownPayments\PurchaseOrderDownPaymentsActions;
use App\Http\Requests\PurchaseOrderDownPaymentsRequest;
use App\Http\Resources\PurchaseOrderDownPaymentsResource;
use App\Models\PurchaseOrderDownPayments;
use Exception;

class PurchaseOrderDownPaymentsController extends BaseController
{
    private $purchaseOrderDownPaymentsActions;

    public function __construct(PurchaseOrderDownPaymentsActions $purchaseOrderDownPaymentsActions)
    {
        parent::__construct();

        $this->purchaseOrderDownPaymentsActions = $purchaseOrderDownPaymentsActions;
    }

    public function store(PurchaseOrderDownPaymentsRequest $purchaseOrderDownPaymentsRequest)
    {
        $request = $purchaseOrderDownPaymentsRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseOrderDownPaymentsActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(PurchaseOrderDownPaymentsRequest $purchaseOrderDownPaymentsRequest)
    {
        $request = $purchaseOrderDownPaymentsRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseOrderDownPaymentsActions->readAny(
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
            $response = PurchaseOrderDownPaymentsResource::collection($result);

            return $response;
        }
    }

    public function read(PurchaseOrderDownPayments $purchaseOrderDownPayments, PurchaseOrderDownPaymentsRequest $purchaseOrderDownPaymentsRequest)
    {
        $request = $purchaseOrderDownPaymentsRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseOrderDownPaymentsActions->read($purchaseOrderDownPayments);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new PurchaseOrderDownPaymentsResource($result);

            return $response;
        }
    }

    public function update(PurchaseOrderDownPayments $purchaseOrderDownPayments, PurchaseOrderDownPaymentsRequest $purchaseOrderDownPaymentsRequest)
    {
        $request = $purchaseOrderDownPaymentsRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->purchaseOrderDownPaymentsActions->update(
                purchaseOrderDownPayments: $purchaseOrderDownPayments,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(PurchaseOrderDownPayments $purchaseOrderDownPayments, PurchaseOrderDownPaymentsRequest $purchaseOrderDownPaymentsRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->purchaseOrderDownPaymentsActions->delete($purchaseOrderDownPayments);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
