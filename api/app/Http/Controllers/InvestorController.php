<?php

namespace App\Http\Controllers;

use App\Actions\Investor\InvestorActions;
use App\Http\Requests\InvestorRequest;
use App\Http\Resources\InvestorResource;
use App\Models\Investor;
use Exception;

class InvestorController extends BaseController
{
    private $investorActions;

    public function __construct(InvestorActions $investorActions)
    {
        parent::__construct();

        $this->investorActions = $investorActions;
    }

    public function store(InvestorRequest $investorRequest)
    {
        $request = $investorRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->investorActions->create($request);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function readAny(InvestorRequest $investorRequest)
    {
        $request = $investorRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->investorActions->readAny(
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
            $response = InvestorResource::collection($result);

            return $response;
        }
    }

    public function read(Investor $investor, InvestorRequest $investorRequest)
    {
        $request = $investorRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->investorActions->read($investor);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        if (is_null($result)) {
            return response()->error($errorMsg);
        } else {
            $response = new InvestorResource($result);

            return $response;
        }
    }

    public function update(Investor $investor, InvestorRequest $investorRequest)
    {
        $request = $investorRequest->validated();

        $result = null;
        $errorMsg = '';

        try {
            $result = $this->investorActions->update(
                investor: $investor,
                data: $request
            );
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return is_null($result) ? response()->error($errorMsg) : response()->success();
    }

    public function delete(Investor $investor, InvestorRequest $investorRequest)
    {
        $result = false;
        $errorMsg = '';

        try {
            $result = $this->investorActions->delete($investor);
        } catch (Exception $e) {
            $errorMsg = app()->environment('production') ? '' : $e->getMessage();
        }

        return ! $result ? response()->error($errorMsg) : response()->success();
    }
}
