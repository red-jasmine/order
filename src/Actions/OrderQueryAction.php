<?php

namespace RedJasmine\Order\Actions;

use RedJasmine\Order\Models\Order;
use RedJasmine\Support\Foundation\Service\HasQueryBuilder;
use Spatie\QueryBuilder\QueryBuilder;

class OrderQueryAction extends AbstractOrderAction
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
