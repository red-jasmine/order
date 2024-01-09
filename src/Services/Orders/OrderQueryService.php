<?php

namespace RedJasmine\Order\Services\Orders;

use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Services\Orders\Actions\AbstractOrderAction;
use RedJasmine\Support\Foundation\Service\HasQueryBuilder;
use Spatie\QueryBuilder\QueryBuilder;

class OrderQueryService extends AbstractOrderAction
{


    use HasQueryBuilder;

    protected string $model = Order::class;


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
        return $this->query()->paginate();

    }


}
