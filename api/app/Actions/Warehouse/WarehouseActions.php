<?php

namespace App\Actions\Warehouse;

use App\Enums\RecordStatusEnum;
use App\Models\Company;
use App\Models\Warehouse;
use App\Traits\CacheHelper;
use App\Traits\LoggerHelper;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class WarehouseActions
{
    use CacheHelper;
    use LoggerHelper;

    public function __construct()
    {
    }

    public function create(array $data): Warehouse
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        try {
            $warehouse = new Warehouse();
            $warehouse->company_id = $data['company_id'];
            $warehouse->branch_id = $data['branch_id'];
            $warehouse->code = $this->generateUniqueCode($data['company_id'], $data['code'], null);
            $warehouse->name = $data['name'];
            $warehouse->address = $data['address'];
            $warehouse->city = $data['city'];
            $warehouse->contact = $data['contact'];
            $warehouse->remarks = $data['remarks'];
            $warehouse->status = $data['status'];
            $warehouse->save();

            DB::commit();

            $this->flushCache();

            return $warehouse;
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
        ?array $with,
        ?bool $withTrashed,

        ?string $search,
        int $companyId,
        ?int $branchId,
        ?int $status,

        ?int $limit
    ) {
        $query = Warehouse::with($with ?? ['company', 'branch'])->withTrashed()
            ->withAggregate('company', 'name')
            ->withAggregate('branch', 'name')
            ->where(function ($query) use ($withTrashed, $search, $companyId, $branchId, $status) {
                if ($withTrashed == true) {
                    $query = $query->withTrashed();
                } else {
                    $query = $query->withoutTrashed();
                }

                if ($search) {
                    $query->search($search);
                }

                $query->whereCompanyId($companyId);

                if ($branchId) {
                    $query->where('branch_id', $branchId);
                }

                if ($status) {
                    $query->where('status', $status);
                }
            });

        $query->orderBy('company_name', 'asc')
            ->orderBy('branch_name', 'asc')
            ->orderBy('name', 'asc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query;
    }

    public function readAny(
        ?bool $useCache,
        ?array $with,
        ?bool $withTrashed,

        ?string $search,
        int $companyId,
        ?int $branchId,
        ?int $status,

        bool $paginate,
        ?int $page,
        ?int $perPage,
        ?int $limit
    ): Paginator|Collection {
        $timer_start = microtime(true);
        $recordsCount = 0;

        try {
            $cacheKey = 'read_'.$companyId.'-'.(empty($search) ? '[empty]' : $search).'-'.$paginate.'-'.$page.'-'.$perPage;
            if ($useCache === true) {
                $cacheResult = $this->readFromCache($cacheKey);

                if (! is_null($cacheResult)) {
                    return $cacheResult;
                }
            }

            $result = null;

            $query = $this->readAnyQuery(
                with: $with,
                withTrashed: $withTrashed,
                search: $search,
                companyId: $companyId,
                branchId: $branchId,
                status: $status,
                limit: $paginate ? null : $limit
            );

            if ($paginate) {
                $perPage = is_numeric($perPage) ? abs($perPage) : Config::get('dcslab.PAGINATION_LIMIT');
                $page = is_numeric($page) ? abs($page) : 1;

                $result = $query->paginate(perPage: $perPage, page: $page);
            } else {
                $result = $query->get();
            }

            $recordsCount = $result->count();

            if ($useCache === true) {
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

    public function read(Warehouse $warehouse): Warehouse
    {
        return $warehouse->load('company', 'branch');
    }

    public function getAllActiveWarehouse(
        ?array $with,
        ?bool $withTrashed,

        ?string $search,
        int $companyId,
        ?int $branchId,
        ?array $includeIds,

        ?int $limit
    ) {
        $timer_start = microtime(true);

        try {
            $query = $this->readAnyQuery(
                with: $with,
                withTrashed: $withTrashed,

                search: $search,
                companyId: $companyId,
                branchId: $branchId,
                status: RecordStatusEnum::ACTIVE->value,

                limit: $limit
            );

            if ($includeIds) {
                $query = $query->orWhereIn('id', $includeIds);
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

    public function update(Warehouse $warehouse, array $data): Warehouse
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        try {
            $warehouse->code = $this->generateUniqueCode($warehouse->company_id, $data['code'], $warehouse->id);
            $warehouse->name = $data['name'];
            $warehouse->address = $data['address'];
            $warehouse->city = $data['city'];
            $warehouse->contact = $data['contact'];
            $warehouse->remarks = $data['remarks'];
            $warehouse->status = $data['status'];
            $warehouse->save();

            DB::commit();

            $this->flushCache();

            return $warehouse->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function delete(Warehouse $warehouse): bool
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        $retval = false;

        try {
            $retval = $warehouse->delete();

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
                $count = $company->warehouses()->withTrashed()->count() + 1 + $tryCount;
                $code = 'WH'.str_pad($count, 3, '0', STR_PAD_LEFT);
                $tryCount++;
            } while (! $this->isUniqueCode($companyId, $code, $exceptId));

            return $code;
        } else {
            return $code;
        }
    }

    public function isUniqueCode(int $companyId, string $code, ?int $exceptId = null): bool
    {
        $result = Warehouse::whereCompanyId($companyId)->where('code', '=', $code);

        if ($exceptId) {
            $result = $result->where('id', '<>', $exceptId);
        }

        return $result->count() == 0 ? true : false;
    }
}
