<?php

namespace App\Http\Controllers;

use App\Actions\Sale\SaleActions;
use App\Http\Requests\SaleRequest;
use App\Http\Resources\SaleResource;
use App\Models\Sale;
use Exception;

class SaleController extends BaseController
{
    private $saleActions;

    public function __construct(SaleActions $saleActions)
    {
        parent::__construct();

        $this->saleActions = $saleActions;
    }

    public function store(SaleRequest $saleRequest)
    {
        $request = $saleRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->saleActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(SaleRequest $saleRequest)
    {
        $request = $saleRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->saleActions->readAny(
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
            $response = SaleResource::collection($result);

            return $response;
        }
    }

    public function read(Sale $sale, SaleRequest $saleRequest)
    {
        $request = $saleRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->saleActions->read($sale);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new SaleResource($result);

            return $response;
        }
    }

    public function update(Sale $sale, SaleRequest $saleRequest)
    {
        $request = $saleRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->saleActions->update(
                sale: $sale,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(Sale $sale, SaleRequest $saleRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->saleActions->delete($sale);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
