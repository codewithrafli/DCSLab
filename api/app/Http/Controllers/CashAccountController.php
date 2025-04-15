<?php

namespace App\Http\Controllers;

use App\Actions\CashAccount\CashAccountActions;
use App\Http\Requests\CashAccountRequest;
use App\Http\Resources\CashAccountResource;
use App\Models\CashAccount;
use Exception;

class CashAccountController extends BaseController
{
    private $cashAccountActions;

    public function __construct(CashAccountActions $cashAccountActions)
    {
        parent::__construct();

        $this->cashAccountActions = $cashAccountActions;
    }

    public function store(CashAccountRequest $cashAccountRequest)
    {
        $request = $cashAccountRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->cashAccountActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(CashAccountRequest $cashAccountRequest)
    {
        $request = $cashAccountRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->cashAccountActions->readAny(
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
            $response = CashAccountResource::collection($result);

            return $response;
        }
    }

    public function read(CashAccount $cashAccount, CashAccountRequest $cashAccountRequest)
    {
        $request = $cashAccountRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->cashAccountActions->read($cashAccount);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new CashAccountResource($result);

            return $response;
        }
    }

    public function update(CashAccount $cashAccount, CashAccountRequest $cashAccountRequest)
    {
        $request = $cashAccountRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->cashAccountActions->update(
                cashAccount: $cashAccount,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(CashAccount $cashAccount, CashAccountRequest $cashAccountRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->cashAccountActions->delete($cashAccount);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
