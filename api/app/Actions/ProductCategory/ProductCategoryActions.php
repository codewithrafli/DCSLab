<?php

namespace App\Actions\ProductCategory;

use App\Models\Company;
use App\Models\ProductCategory;
use App\Traits\CacheHelper;
use App\Traits\LoggerHelper;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductCategoryActions
{
    use CacheHelper;
    use LoggerHelper;

    public function __construct()
    {
    }

    public function create(array $data): ProductCategory
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        try {
            $productCategory = new ProductCategory();
            $productCategory->company_id = $data['company_id'];
            $productCategory->code = $this->generateUniqueCode($data['company_id'], $data['code'], null);
            $productCategory->name = $data['name'];
            $productCategory->type = $data['type'];
            $productCategory->save();

            DB::commit();

            $this->flushCache();

            return $productCategory;
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
        $query = ProductCategory::select('product_categories.*')->withTrashed()
            ->with(['company'])
            ->join('companies', 'companies.id', '=', 'product_categories.company_id')
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
            ->orderBy('product_categories.name', 'asc');

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

    public function read(ProductCategory $productCategory): ProductCategory
    {
        return $productCategory->load('company')->first();
    }

    public function getAllActiveProductCategory(
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

    public function update(ProductCategory $productCategory, array $data): ProductCategory
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        try {
            $productCategory->code = $this->generateUniqueCode($productCategory->company_id, $data['code'], $productCategory->id);
            $productCategory->name = $data['name'];
            $productCategory->type = $data['type'];
            $productCategory->save();

            DB::commit();

            $this->flushCache();

            return $productCategory->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function delete(ProductCategory $productCategory): bool
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        $retval = false;

        try {
            $retval = $productCategory->delete();

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
                $count = $company->productCategories()->withTrashed()->count() + 1 + $tryCount;
                $code = 'PC'.str_pad($count, 3, '0', STR_PAD_LEFT);
                $tryCount++;
            } while (! $this->isUniqueCode($companyId, $code, $exceptId));

            return $code;
        } else {
            return $code;
        }
    }

    public function isUniqueCode(int $companyId, string $code, ?int $exceptId): bool
    {
        $result = ProductCategory::whereCompanyId($companyId)->where('code', '=', $code);

        if ($exceptId) {
            $result = $result->where('id', '<>', $exceptId);
        }

        return $result->count() == 0 ? true : false;
    }
}
