<?php

namespace App\Actions\CustomerGroup;

use App\DTOs\ExecuteDTO;
use App\Models\Company;
use App\Models\CustomerGroup;
use App\Traits\CacheHelper;
use App\Traits\LoggerHelper;
use Exception;
use Illuminate\Support\Facades\Config;

class CustomerGroupActions
{
    use CacheHelper;
    use LoggerHelper;

    public function __construct()
    {
    }

    public function create(array $data): CustomerGroup
    {
        $timer_start = microtime(true);

        try {
            $customerGroup = new CustomerGroup();
            $customerGroup->company_id = $data['company_id'];
            $customerGroup->code = $this->generateUniqueCode($data['company_id'], $data['code'], null);
            $customerGroup->name = $data['name'];
            $customerGroup->max_open_invoice = $data['max_open_invoice'];
            $customerGroup->max_outstanding_invoice = $data['max_outstanding_invoice'];
            $customerGroup->max_invoice_age = $data['max_invoice_age'];
            $customerGroup->payment_term_type = $data['payment_term_type'];
            $customerGroup->payment_term = $data['payment_term'];
            $customerGroup->selling_point = $data['selling_point'];
            $customerGroup->selling_point_multiple = $data['selling_point_multiple'];
            $customerGroup->sell_at_cost = $data['sell_at_cost'];
            $customerGroup->price_markup_percent = $data['price_markup_percent'];
            $customerGroup->price_markup_nominal = $data['price_markup_nominal'];
            $customerGroup->price_markdown_percent = $data['price_markdown_percent'];
            $customerGroup->price_markdown_nominal = $data['price_markdown_nominal'];
            $customerGroup->rounding_type = $data['rounding_type'];
            $customerGroup->rounding_digit = $data['rounding_digit'];
            $customerGroup->remarks = $data['remarks'];
            $customerGroup->save();

            $this->flushCache();

            return $customerGroup;
        } catch (Exception $e) {
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function readAny(
        bool $withTrashed,
        int $companyId,

        ?string $search,
        ?int $includeId,

        ?ExecuteDTO $execute
    ) {
        $query = CustomerGroup::with(['company'])->select('customer_groups.*')
            ->where('customer_groups.company_id', $companyId)
            ->withTrashed();

        $query->where(function ($query) use (
            $withTrashed,
            $search,
            $includeId,
        ) {
            $query->where(function ($query) use (
                $withTrashed,
                $search,
            ) {
                $query->withoutTrashed();
                if ($withTrashed) {
                    $query->withTrashed();
                }

                if ($search) {
                    $query->search($search);
                }
            });

            if ($includeId) {
                $query->orWhere('customer_groups.id', $includeId);
            }
        });

        if ($includeId) {
            $query->orderByRaw('FIELD(customer_groups.id, '.$includeId.') desc');
        }
        $query->orderBy('customer_groups.name', 'asc');

        if ($execute) {
            $timer_start = microtime(true);
            $recordsCount = 0;

            try {
                $cacheParams = [
                    $withTrashed ? 'true' : 'false',
                    empty($search) ? '[empty]' : $search,
                    $companyId,
                    $includeId ?? '[null]',
                    $execute->pagination ? 'true' : 'false',
                    $execute->pagination?->page ?? '[null]',
                    $execute->pagination?->perPage ?? '[null]',
                    $execute->get?->limit ?? '[null]',
                ];

                $cacheKey = 'readAny_'.implode('-', $cacheParams);

                if ($execute->useCache) {
                    $cacheData = $this->readFromCache($cacheKey);
                    if ($cacheData !== Config::get('dcslab.ERROR_RETURN_VALUE')) {
                        return $cacheData;
                    }
                }

                if ($execute->pagination) {
                    $result = $query->paginate(
                        perPage: $execute->pagination->perPage,
                        columns: ['*'],
                        pageName: 'page',
                        page: $execute->pagination->page
                    );
                } else {
                    if ($execute->get?->limit) {
                        $query->limit($execute->get->limit);
                    }
                    $result = $query->get();
                }

                $recordsCount = $result->count();

                if ($execute->useCache) {
                    $this->saveToCache($cacheKey, $result);
                }

                return $result;
            } catch (Exception $e) {
                $this->loggerDebug(__METHOD__, $e);
                throw $e;
            } finally {
                $execution_time = microtime(true) - $timer_start;
                $this->loggerPerformance(__METHOD__, $execution_time, $recordsCount);
            }
        }

        return $query;
    }

    public function read(CustomerGroup $customerGroup): CustomerGroup
    {
        return $customerGroup->load('company');
    }

    public function update(CustomerGroup $customerGroup, array $data): CustomerGroup
    {
        $timer_start = microtime(true);

        try {
            $customerGroup->code = $this->generateUniqueCode($customerGroup->company_id, $data['code'], $customerGroup->id);
            $customerGroup->name = $data['name'];
            $customerGroup->max_open_invoice = $data['max_open_invoice'];
            $customerGroup->max_outstanding_invoice = $data['max_outstanding_invoice'];
            $customerGroup->max_invoice_age = $data['max_invoice_age'];
            $customerGroup->payment_term_type = $data['payment_term_type'];
            $customerGroup->payment_term = $data['payment_term'];
            $customerGroup->selling_point = $data['selling_point'];
            $customerGroup->selling_point_multiple = $data['selling_point_multiple'];
            $customerGroup->sell_at_cost = $data['sell_at_cost'];
            $customerGroup->price_markup_percent = $data['price_markup_percent'];
            $customerGroup->price_markup_nominal = $data['price_markup_nominal'];
            $customerGroup->price_markdown_percent = $data['price_markdown_percent'];
            $customerGroup->price_markdown_nominal = $data['price_markdown_nominal'];
            $customerGroup->rounding_type = $data['rounding_type'];
            $customerGroup->rounding_digit = $data['rounding_digit'];
            $customerGroup->remarks = $data['remarks'];
            $customerGroup->save();

            $this->flushCache();

            return $customerGroup->refresh();
        } catch (Exception $e) {
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function delete(CustomerGroup $customerGroup): bool
    {
        $timer_start = microtime(true);

        $retval = false;

        try {
            $retval = $customerGroup->delete();

            $this->flushCache();

            return $retval;
        } catch (Exception $e) {
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function generateUniqueCode(int $companyId, string $code, ?int $exceptId): string
    {
        if ($code == config('dcslab.KEYWORDS.AUTO')) {
            $company = Company::find($companyId);

            $tryCount = 0;
            do {
                $count = $company->customerGroups()->withTrashed()->count() + 1 + $tryCount;
                $code = 'CG'.str_pad($count, 3, '0', STR_PAD_LEFT);
                $tryCount++;
            } while (! $this->isUniqueCode($companyId, $code, $exceptId));

            return $code;
        } else {
            return $code;
        }
    }

    public function isUniqueCode(int $companyId, string $code, ?int $exceptId): bool
    {
        $result = CustomerGroup::whereCompanyId($companyId)->where('code', '=', $code);

        if ($exceptId) {
            $result = $result->where('id', '<>', $exceptId);
        }

        return $result->count() == 0 ? true : false;
    }
}
