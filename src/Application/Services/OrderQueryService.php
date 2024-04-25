<?php

namespace RedJasmine\Order\Application\Services;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use RedJasmine\Order\Application\UserCases\Queries\OrderAllQuery;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Infrastructure\ReadRepositories\Mysql\OrderReadRepository;

class OrderQueryService
{
    public function __construct(protected OrderReadRepository $readRepository)
    {
        $this->readRepository->setAllowedFilters($this->allowedFilters);
        $this->readRepository->setAllowedFields($this->allowedFields);
        $this->readRepository->setAllowedIncludes($this->allowedIncludes);
        $this->readRepository->setAllowedSorts($this->allowedSorts);

    }

    // 每个查询
    protected array $allowedFilters = [];


    protected array $allowedIncludes = [
        'products',
        'payments',
        'info',
        'products.info',
        'logistics',
        'address'
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

    /**
     * @param OrderAllQuery $allQuery
     *
     * @return LengthAwarePaginator<Order>
     */
    public function paginate(OrderAllQuery $allQuery) : LengthAwarePaginator
    {
        return $this->readRepository->setQueryCallbacks($this->queryCallbacks)->findAll($allQuery->query);
    }

    public function find(int $id, array $query = []) : Order
    {
        return $this->readRepository->setQueryCallbacks($this->queryCallbacks)->findById($id, $query);
    }

}
