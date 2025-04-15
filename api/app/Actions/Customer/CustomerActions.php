<?php

namespace App\Actions\Customer;

use App\Models\Company;
use App\Models\Customer;
use App\Traits\CacheHelper;
use App\Traits\LoggerHelper;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomerActions
{
    use CacheHelper;
    use LoggerHelper;

    public function __construct()
    {
    }

    public function create(array $data): Customer
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        try {
            $customer = new Customer();
            $customer->company_id = $data['company_id'];
            $customer->customer_group_id = $data['customer_group_id'];
            $customer->user_id = $data['user_id'];
            $customer->code = $this->generateUniqueCode($data['company_id'], $data['code'], null);
            $customer->is_member = $data['is_member'];
            $customer->name = $data['name'];
            $customer->zone = $data['zone'];
            $customer->max_open_invoice = $data['max_open_invoice'];
            $customer->max_outstanding_invoice = $data['max_outstanding_invoice'];
            $customer->max_invoice_age = $data['max_invoice_age'];
            $customer->payment_term_type = $data['payment_term_type'];
            $customer->payment_term = $data['payment_term'];
            $customer->taxable_enterprise = $data['taxable_enterprise'];
            $customer->tax_id = $data['tax_id'];
            $customer->status = $data['status'];
            $customer->remarks = $data['remarks'];
            $customer->save();

            DB::commit();

            $this->flushCache();

            return $customer;
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
        $query = Customer::with('company')->withTrashed()
            ->withAggregate('company', 'name')
            ->where(function ($query) use ($withTrashed, $search, $companyId) {
                if ($withTrashed == true) {
                    $query = $query->withTrashed();
                } else {
                    $query = $query->withoutTrashed();
                }

                if ($search) {
                    $query->search($search);
                }

                $query->whereCompanyId($companyId);
            });

        $query->orderBy('company_name', 'asc')
            ->orderBy('name', 'asc');

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
            $cacheKey = 'readAny_'.$companyId.'-'.$cacheSearch.'-'.$paginate.'-'.$page.'-'.$perPage;
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

    public function read(Customer $customer): Customer
    {
        return $customer->with('company', 'user', 'customer')->first();
    }

    public function getAllActiveCustomer(
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
                $query->orderByRaw('FIELD(id, '.implode(',', $includeIds).') desc');
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

    public function update(Customer $customer, array $data): Customer
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        try {
            $customer->code = $this->generateUniqueCode($customer->company_id, $data['code'], $customer->id);
            $customer->is_member = $data['is_member'];
            $customer->name = $data['name'];
            $customer->zone = $data['zone'];
            $customer->max_open_invoice = $data['max_open_invoice'];
            $customer->max_outstanding_invoice = $data['max_outstanding_invoice'];
            $customer->max_invoice_age = $data['max_invoice_age'];
            $customer->payment_term_type = $data['payment_term_type'];
            $customer->payment_term = $data['payment_term'];
            $customer->taxable_enterprise = $data['taxable_enterprise'];
            $customer->tax_id = $data['tax_id'];
            $customer->status = $data['status'];
            $customer->remarks = $data['remarks'];
            $customer->save();

            DB::commit();

            $this->flushCache();

            return $customer->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function delete(Customer $customer): bool
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        $retval = false;

        try {
            $retval = $customer->delete();

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
                $count = $company->customers()->withTrashed()->count() + 1 + $tryCount;
                $code = 'WH'.str_pad($count, 3, '0', STR_PAD_LEFT);
                $tryCount++;
            } while (! $this->isUniqueCode($companyId, $code, $exceptId));

            return $code;
        } else {
            return $code;
        }
    }

    public function isUniqueCode(int $companyId, string $code, ?int $exceptId): bool
    {
        $result = Customer::whereCompanyId($companyId)->where('code', '=', $code);

        if ($exceptId) {
            $result = $result->where('id', '<>', $exceptId);
        }

        return $result->count() == 0 ? true : false;
    }
}
