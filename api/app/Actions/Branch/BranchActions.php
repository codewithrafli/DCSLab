<?php

namespace App\Actions\Branch;

use App\Actions\Company\CompanyActions;
use App\Models\Branch;
use App\Models\Company;
use App\Traits\CacheHelper;
use App\Traits\LoggerHelper;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class BranchActions
{
    use CacheHelper;
    use LoggerHelper;

    public function __construct()
    {
    }

    public function create(array $data): Branch
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        try {
            if ($data['is_main'] == true) {
                $this->resetMainByCompany($data['company_id']);
            }

            $branch = new Branch();
            $branch->company_id = $data['company_id'];
            $branch->code = $this->generateUniqueCode($data['company_id'], $data['code']);
            $branch->name = $data['name'];
            $branch->address = $data['address'];
            $branch->city = $data['city'];
            $branch->contact = $data['contact'];
            $branch->is_main = $data['is_main'];
            $branch->remarks = $data['remarks'];
            $branch->status = $data['status'];
            $branch->save();

            DB::commit();

            $this->flushCache();

            return $branch;
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

        int $companyId,
        ?string $search,
        ?bool $isMain,
        ?int $status,

        ?int $limit
    ) {
        $relationship = ['company'];
        if ($with) {
            $relationship = $with;
        }

        $query = Branch::with($relationship)->withTrashed()
            ->where(function ($query) use ($withTrashed, $companyId, $search, $isMain, $status) {
                if ($withTrashed !== null) {
                    if ($withTrashed) {
                        $query = $query->withTrashed();
                    } else {
                        $query = $query->withoutTrashed();
                    }
                } else {
                    $query = $query->withoutTrashed();
                }

                $query->whereCompanyId($companyId);

                if ($search) {
                    $query->search($search);
                }

                if ($isMain !== null) {
                    $query->where('is_main', '=', $isMain);
                }

                if ($status !== null) {
                    $query->where('status', '=', $status);
                }
            })->latest();

        if ($limit) {
            $query = $query->limit($limit);
        }

        return $query;
    }

    public function readAny(
        int $companyId,
        bool $useCache,
        ?array $with,
        ?bool $withTrashed,

        ?string $search,
        ?string $isMain,
        ?int $status,

        bool $paginate = true,
        ?int $page = 1,
        ?int $perPage = 10,
    ): Paginator|Collection {
        $timer_start = microtime(true);
        $recordsCount = 0;

        try {
            $cacheSearch = empty($search) ? '[empty]' : $search;
            $cacheKey = 'readAny_'.$companyId.'_'.$cacheSearch.'-'.$paginate.'-'.$page.'-'.$perPage;
            if ($useCache) {
                $cacheResult = $this->readFromCache($cacheKey);

                if (! is_null($cacheResult)) {
                    return $cacheResult;
                }
            }

            $result = null;

            $query = $this->readAnyQuery(
                companyId: $companyId,
                with: $with,
                withTrashed: $withTrashed,
                search: $search,
                isMain: $isMain,
                status: $status,
                limit: null
            );

            if ($paginate) {
                $perPage = is_numeric($perPage) ? abs($perPage) : Config::get('dcslab.PAGINATION_LIMIT');
                $page = is_numeric($page) ? abs($page) : 1;

                $result = $query->paginate(perPage: $perPage, page: $page);
            } else {
                $result = $query->get();
            }

            $recordsCount = $result->count();

            $this->saveToCache($cacheKey, $result);

            return $result;
        } catch (Exception $e) {
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time, $recordsCount);
        }
    }

    public function read(Branch $branch): Branch
    {
        return $branch->load('company');
    }

    public function getByCompany(int $companyId = 0, ?Company $company = null): Collection
    {
        if (! is_null($company)) {
            return $company->branches;
        }

        if ($companyId != 0) {
            return Branch::where('company_id', '=', $companyId)->where('status', '=', 1)->get();
        }

        return null;
    }

    public function getMainByCompany(int $companyId = 0, ?Company $company = null): Branch
    {
        if (! is_null($company)) {
            return $company->branches()->where('is_main', '=', true)->first();
        }

        if ($companyId != 0) {
            return Branch::where('company_id', '=', $companyId)->where('is_main', '=', true)->first();
        }

        return null;
    }

    public function update(
        Branch $branch,
        array $data,
    ): Branch {
        DB::beginTransaction();
        $timer_start = microtime(true);

        try {
            if ($data['is_main'] == true) {
                $this->resetMainByCompany($branch->company_id);
            }

            $branch->code = $this->generateUniqueCode($branch->company_id, $data['code'], $branch->id);
            $branch->name = $data['name'];
            $branch->address = $data['address'];
            $branch->city = $data['city'];
            $branch->contact = $data['contact'];
            $branch->is_main = $data['is_main'];
            $branch->remarks = $data['remarks'];
            $branch->status = $data['status'];
            $branch->save();

            DB::commit();

            $this->flushCache();

            return $branch->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function resetMainByCompany(int $companyId): bool
    {
        $timer_start = microtime(true);

        try {
            $company = (new CompanyActions())->getById($companyId);

            return $company->branches()->update(['is_main' => false]);
        } catch (Exception $e) {
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function delete(Branch $branch): bool
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        $retval = false;
        try {
            $retval = $branch->delete();

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

    public function generateUniqueCode(int $companyId, string $code, ?int $exceptId = null): string
    {
        if ($code == config('dcslab.KEYWORDS.AUTO')) {
            $company = Company::find($companyId);

            $tryCount = 0;
            do {
                $count = $company->branches()->withTrashed()->count() + 1 + $tryCount;
                $code = 'BC'.str_pad($count, 3, '0', STR_PAD_LEFT);
                $tryCount++;
            } while (! $this->isUniqueCode($companyId, $code, $exceptId));

            return $code;
        } else {
            return $code;
        }
    }

    public function isUniqueCode(int $companyId, string $code, ?int $exceptId = null): bool
    {
        $company = Company::find($companyId);

        if ($company->branches()->count() == 0) {
            return true;
        }

        $query = Branch::whereCompanyId($companyId)->where('code', '=', $code);

        if ($exceptId) {
            $query = $query->where('id', '<>', $exceptId);
        }

        return $query->doesntExist();
    }
}
