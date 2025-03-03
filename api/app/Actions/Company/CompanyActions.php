<?php

namespace App\Actions\Company;

use App\Enums\RecordStatus;
use App\Models\Company;
use App\Models\User;
use App\Traits\CacheHelper;
use App\Traits\LoggerHelper;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
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
        DB::beginTransaction();
        $timer_start = microtime(true);

        try {
            if ($data['default'] == true) {
                $this->resetDefault($user);
            }

            $company = new Company();
            $company->code = $this->generateUniqueCode($user, $data['code']);
            $company->name = $data['name'];
            $company->address = $data['address'];
            $company->default = $data['default'];
            $company->status = $data['status'];
            $company->save();

            $user->companies()->attach([$company->id]);

            DB::commit();

            $this->flushCache();

            return $company;
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
        User $user,
        ?array $with,
        ?bool $withTrashed,

        ?string $search,
        ?bool $default,
        ?int $status,

        ?int $limit
    ) {
        $query = Company::with($with ?? ['branches'])->withTrashed()
            ->where(function ($query) use ($user, $withTrashed, $search, $default, $status) {
                if ($withTrashed == true) {
                    $query = $query->withTrashed();
                } else {
                    $query = $query->withoutTrashed();
                }

                if ($search) {
                    $query = $query->search($search);
                }

                $query = $query->whereIn('id', $user->companies()->pluck('company_id'));

                if ($default !== null) {
                    $query = $query->where('default', $default);
                }

                if ($status !== null) {
                    $query = $query->where('status', $status);
                }
            });

        $query->orderBy('name', 'asc');

        if ($limit) {
            $query->take($limit);
        }

        return $query;
    }

    public function readAny(
        User $user,
        ?bool $useCache,
        ?array $with,
        ?bool $withTrashed,

        ?string $search,
        ?bool $default,
        ?int $status,

        bool $paginate,
        ?int $page,
        ?int $perPage,
        ?int $limit
    ): Paginator|Collection {
        $timer_start = microtime(true);
        $recordsCount = 0;

        try {
            $cacheKey = 'readAny_'.$user->id.'-'.(empty($search) ? '[empty]' : $search).'-'.$paginate.'-'.$page.'-'.$perPage;
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
                user: $user,
                default: $default,
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

    public function read(Company $company): Company
    {
        return $company->load('branches');
    }

    public function getAllActive(
        User $user,
        ?array $with,
        ?string $search,
        ?array $includeIds,
        ?int $limit
    ) {
        $timer_start = microtime(true);

        try {
            $query = $this->readAnyQuery(
                user: $user,
                with: $with,
                withTrashed: false,
                search: $search,
                default: null,
                status: RecordStatus::ACTIVE->value,
                limit: $limit
            );

            if (in_array('branches', $with)) {
                $query = $query->with(['branches' => function ($query) {
                    $query->where('status', '=', 1);
                }]);
            }

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

    public function isDefault(Company $company): bool
    {
        $result = $company->default;

        return is_null($result) ? false : $result;
    }

    public function getById(int $companyId): Company
    {
        return Company::find($companyId)->first();
    }

    public function getDefault(User $user): Company
    {
        return $user->companies()->where('default', '=', true)->first();
    }

    public function update(User $user, Company $company, array $data): Company
    {
        DB::beginTransaction();
        $timer_start = microtime(true);

        try {
            if ($data['default'] == true) {
                $this->resetDefault($user);
                $company->refresh();
            }

            $company->code = $data['code'];
            $company->name = $data['name'];
            $company->address = $data['address'];
            $company->default = $data['default'];
            $company->status = $data['status'];
            $company->save();

            DB::commit();

            $this->flushCache();

            return $company->refresh();
        } catch (Exception $e) {
            DB::rollBack();
            $this->loggerDebug(__METHOD__, $e);
            throw $e;
        } finally {
            $execution_time = microtime(true) - $timer_start;
            $this->loggerPerformance(__METHOD__, $execution_time);
        }
    }

    private function resetDefault(User $user)
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

    public function generateUniqueCode(User $user, string $code, ?int $exceptId = null): string
    {
        if ($code == config('dcslab.KEYWORDS.AUTO')) {
            $tryCount = 0;
            do {
                $count = $user->companies()->withTrashed()->count() + 1 + $tryCount;
                $code = 'CP'.str_pad($count, 3, '0', STR_PAD_LEFT);
                $tryCount++;
            } while (! $this->isUniqueCode($user, $code, $exceptId));

            return $code;
        } else {
            return $code;
        }
    }

    public function isUniqueCode(User $user, string $code, ?int $exceptId = null): bool
    {
        if ($user->companies->count() == 0) {
            return true;
        }

        $query = $user->companies()->where('code', '=', $code);
        if ($exceptId) {
            $query = $query->where('company_id', '<>', $exceptId);
        }

        return $query->doesntExist();
    }
}
