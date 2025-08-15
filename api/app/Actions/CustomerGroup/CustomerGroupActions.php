<?php

namespace App\Actions\CustomerGroup;

use App\Models\Company;
use App\Models\CustomerGroup;
use App\Traits\CacheHelper;
use App\Traits\LoggerHelper;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerGroupActions
{
    use CacheHelper;
    use LoggerHelper;

    public function __construct() {}

    public function create(array $data): CustomerGroup
    {
        DB::beginTransaction();
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

            DB::commit();

            $this->flushCache();

            return $customerGroup;
        } catch (Exception $e) {
            DB::rollBack();
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    private function readAnyQuery(
        ?bool $withTrashed,

        ?string $search,
        int $companyId,

        ?int $limit
    ) {
        $query = CustomerGroup::select('customer_groups.*')->withTrashed()
            ->with(['company'])
            ->join('companies', 'companies.id', '=', 'customer_groups.company_id')
            ->where(function ($query) use ($withTrashed, $search, $companyId) {
                if ($withTrashed == true) {
                    $query->withTrashed();
                } else {
                    $query->withoutTrashed();
                }

                if ($search) {
                    $query->search($search);
                }

                $query->whereCompanyId($companyId);
            });

        $query->orderBy('companies.name', 'asc')
            ->orderBy('customer_groups.name', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function readAny(
        ?bool $useCache,
        ?bool $withTrashed,

        ?string $search,
        int $companyId,

        bool $paginate,
        ?int $page,
        ?int $perPage,
        ?int $limit
    ): Paginator|Collection {
        $timer_start = microtime(true);
        $recordsCount = 0;

        try {
            $cacheSearch = empty($search) ? '[empty]' : $search;
            $cacheKey = 'readAny_' . $companyId . '-' . $cacheSearch . '-' . $paginate . '-' . $page . '-' . $perPage;
            if ($useCache === true) {
                $cacheResult = $this->readFromCache($cacheKey);

                if (! is_null($cacheResult)) return $cacheResult;
            }

            $result = null;

            $query = $this->readAnyQuery(
                withTrashed: $withTrashed,
                search: $search,
                companyId: $companyId,
                limit: $paginate ? null : $limit
            );

            if ($paginate) {
                $result = $query->paginate(perPage: $perPage, page: $page);
            } else {
                $result = $query->get();
            }

            $recordsCount = $result->count();

            if ($useCache === true) $this->saveToCache($cacheKey, $result);

            return $result;
        } catch (Exception $e) {
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time, $recordsCount);
        }
    }

    public function read(CustomerGroup $customerGroup): CustomerGroup
    {
        return $customerGroup->load('company');
    }

    public function getAllActiveCustomerGroup(
        ?array $with,
        ?bool $withTrashed,

        ?string $search,
        int $companyId,
        ?array $includeIds,

        ?int $limit
    ) {
        $timer_start = microtime(true);

        try {
            $query = $this->readAnyQuery(
                withTrashed: $withTrashed,

                search: $search,
                companyId: $companyId,

                limit: $limit
            );

            if ($includeIds) {
                $query = $query->orWhereIn('id', $includeIds);

                $orders = $query->getQuery()->orders;
                $query->reorder();
                $query->orderByRaw('FIELD(id, ' . implode(',', $includeIds) . ') desc');
                if (! empty($orders)) {
                    foreach ($orders as $order) {
                        $query->orderBy($order['column'], $order['direction']);
                    }
                }
            }

            return $query->get();
        } catch (Exception $e) {
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function update(CustomerGroup $customerGroup, array $data): CustomerGroup
    {
        DB::beginTransaction();
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

            DB::commit();

            $this->flushCache();

            return $customerGroup->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function delete(CustomerGroup $customerGroup): bool
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        $retval = false;

        try {
            $retval = $customerGroup->delete();

            DB::commit();

            $this->flushCache();

            return $retval;
        } catch (Exception $e) {
            DB::rollBack();
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
                $code = 'CG' . str_pad($count, 3, '0', STR_PAD_LEFT);
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
