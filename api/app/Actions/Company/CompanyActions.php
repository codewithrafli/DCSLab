<?php

namespace App\Actions\Company;

use App\DTOs\ExecuteDTO;
use App\Models\Company;
use App\Models\User;
use App\Traits\CacheHelper;
use App\Traits\LoggerHelper;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CompanyActions
{
    use CacheHelper;
    use LoggerHelper;

    public function __construct()
    {
    }

    public function create(User $user, array $data): Company
    {
        $timer_start = microtime(true);

        try {
            $company = new Company();
            $company->code = $data['code'];
            $company->name = $data['name'];
            $company->address = $data['address'];
            $company->default = $data['default'];
            $company->status = $data['status'];
            $company->save();

            $user->companies()->attach([$company->id]);

            $this->flushCache();

            return $company;
        } catch (Exception $e) {
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function readAny(
        User $user,
        bool $withTrashed,

        ?string $search,
        ?bool $default,
        ?int $status,
        ?int $includeId,

        ?ExecuteDTO $execute
    ) {
        $query = Company::select('companies.*')
            ->whereIn('companies.id', $user->companies()->pluck('company_id'))
            ->withTrashed();

        $query->where(function ($query) use ($withTrashed, $search, $default, $status, $includeId) {
            $query->where(function ($query) use ($withTrashed, $search, $default, $status) {
                $query->withoutTrashed();
                if ($withTrashed == true) $query->withTrashed();

                if ($search) {
                    $query->search($search);
                }

                if ($default !== null) {
                    $query->where('companies.default', $default);
                }

                if ($status !== null) {
                    $query->where('companies.status', $status);
                }
            });

            if ($includeId) {
                $query->orWhere('companies.id', $includeId);
            }
        });

        if ($includeId) $query->orderByRaw('FIELD(companies.id, '.$includeId.') desc');
        $query->orderBy('companies.name', 'asc');

        if ($execute) {
            $timer_start = microtime(true);
            $recordsCount = 0;

            try {
                $cacheParams = [
                    $user->id,
                    $withTrashed,
                    empty($search) ? '[empty]' : $search,
                    $default,
                    $status,
                    $includeId,
                    $execute->pagination ? true : false,
                    $execute->pagination?->page,
                    $execute->pagination?->perPage,
                    $execute->get?->limit,
                ];

                $cacheKey = 'readAny_'.implode('-', $cacheParams);

                if ($execute->useCache) {
                    $cacheData = $this->readFromCache($cacheKey);
                    if ($cacheData !== Config::get('dcslab.ERROR_RETURN_VALUE')) return $cacheData;
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

    public function read(Company $company): Company
    {
        return $company->load('branches');
    }

    public function getById(int $companyId): Company
    {
        return Company::find($companyId)->first();
    }

    public function isDefault(Company $company): bool
    {
        $result = $company->default;

        return is_null($result) ? false : $result;
    }

    public function update(Company $company, array $data): Company
    {
        $timer_start = microtime(true);

        try {
            $company->code = $data['code'];
            $company->name = $data['name'];
            $company->address = $data['address'];
            $company->default = $data['default'];
            $company->status = $data['status'];
            $company->save();

            $this->flushCache();

            return $company;
        } catch (Exception $e) {
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function resetDefault(User $user)
    {
        $timer_start = microtime(true);

        try {
            return $user->companies()->update(['default' => 0]);
        } catch (Exception $e) {
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    public function delete(Company $company): bool
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        $retval = false;

        try {
            $retval = $company->delete();

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

    public function generateUniqueCode(User $user, string $code, ?int $exceptId): string
    {
        $tryCount = 0;
        do {
            $count = $user->companies()->withTrashed()->count() + 1 + $tryCount;
            $code = 'CP'.str_pad($count, 3, '0', STR_PAD_LEFT);
            $tryCount++;
        } while (! $this->isUniqueCode($user, $code, $exceptId));

        return $code;
    }

    public function isUniqueCode(User $user, string $code, ?int $exceptId): bool
    {
        if ($user->companies->count() == 0) return true;

        $query = $user->companies()->where('code', '=', $code);
        if ($exceptId) {
            $query->where('companies.id', '<>', $exceptId);
        }

        return $query->doesntExist();
    }
}
