<?php

namespace RedJasmine\Order\Application\Services;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use RedJasmine\Order\Application\UserCases\Queries\OrderAllQuery;
use RedJasmine\Order\Infrastructure\ReadRepositories\Mysql\OrderReadRepository;

class OrderQueryService
{
    public function __construct(protected OrderReadRepository $readRepository)
    {

        $this->readRepository->allowedFilters($this->allowedFilters);
        $this->readRepository->allowedFields($this->allowedFields);
        $this->readRepository->allowedIncludes($this->allowedIncludes);
        $this->readRepository->allowedSorts($this->allowedSorts);

    }

    // 每个查询
    protected array $allowedFilters = [];


    protected array $allowedIncludes = [
        'products',
        'payments',
        'info',
        'products.info',
        'logistics'
    ];
    protected array $allowedFields   = [];
    protected array $allowedSorts    = [];


    /**
     * @var array
     */
    protected array $queryCallbacks = [];


    public function withQuery(Closure $queryCallback = null) : static
    {
        $this->queryCallbacks[] = $queryCallback;
        return $this;
    }

    public function callQueryCallbacks($query)
    {
        foreach ($this->queryCallbacks as $callback) {
            if ($callback) {
                $callback($query);
            }
        }
        return $query;
    }

    public function query()
    {
        $this->callQueryCallbacks($this->readRepository->query());

        return $this->readRepository->query();
    }


    public function paginate(OrderAllQuery $allQuery) : LengthAwarePaginator
    {

        return $this->readRepository
            ->queryCallbacks($this->queryCallbacks)
            ->findAll($allQuery->query);

    }

    public function findById(int $id)
    {
        return $this->readRepository
            ->queryCallbacks($this->queryCallbacks)
            ->findById($id);

    }

}
