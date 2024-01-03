<?php

namespace RedJasmine\Order\Services\Orders;

use RedJasmine\Order\Models\Order;
use RedJasmine\Order\OrderService;
use RedJasmine\Support\Traits\Services\HasQueryBuilder;
use RedJasmine\Support\Traits\Services\ServiceExtends;
use Spatie\QueryBuilder\QueryBuilder;

class OrderQueryService
{

    use HasQueryBuilder;

    protected string $model = Order::class;

    public function __construct(protected OrderService $service)
    {
    }


    use ServiceExtends;

    public function includes() : array
    {
        return [
            'info', 'address', 'products', 'products.info'
        ];
    }

    /**
     * @return QueryBuilder|Order
     */
    public function query() : QueryBuilder
    {
        return $this->queryBuilder();
    }


    public function find(int $id) : Order
    {
        return $this->query()->findOrFail($id);
    }

    public function lists()
    {
        $this->query();
    }


}
