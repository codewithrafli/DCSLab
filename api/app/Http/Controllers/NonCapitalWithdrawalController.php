<?php

namespace App\Http\Controllers;

use App\Actions\NonCapitalWithdrawal\NonCapitalWithdrawalActions;
use App\Http\Requests\NonCapitalWithdrawalRequest;
use App\Http\Resources\NonCapitalWithdrawalResource;
use App\Models\NonCapitalWithdrawal;
use Exception;

class NonCapitalWithdrawalController extends BaseController
{
    private $nonCapitalWithdrawalActions;

    public function __construct(NonCapitalWithdrawalActions $nonCapitalWithdrawalActions)
    {
        parent::__construct();

        $this->nonCapitalWithdrawalActions = $nonCapitalWithdrawalActions;
    }

    public function store(NonCapitalWithdrawalRequest $nonCapitalWithdrawalRequest)
    {
        $request = $nonCapitalWithdrawalRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalWithdrawalActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(NonCapitalWithdrawalRequest $nonCapitalWithdrawalRequest)
    {
        $request = $nonCapitalWithdrawalRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalWithdrawalActions->readAny(
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
            $response = NonCapitalWithdrawalResource::collection($result);

            return $response;
        }
    }

    public function read(NonCapitalWithdrawal $nonCapitalWithdrawal, NonCapitalWithdrawalRequest $nonCapitalWithdrawalRequest)
    {
        $request = $nonCapitalWithdrawalRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalWithdrawalActions->read($nonCapitalWithdrawal);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new NonCapitalWithdrawalResource($result);

            return $response;
        }
    }

    public function update(NonCapitalWithdrawal $nonCapitalWithdrawal, NonCapitalWithdrawalRequest $nonCapitalWithdrawalRequest)
    {
        $request = $nonCapitalWithdrawalRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalWithdrawalActions->update(
                nonCapitalWithdrawal: $nonCapitalWithdrawal,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(NonCapitalWithdrawal $nonCapitalWithdrawal, NonCapitalWithdrawalRequest $nonCapitalWithdrawalRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->nonCapitalWithdrawalActions->delete($nonCapitalWithdrawal);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
