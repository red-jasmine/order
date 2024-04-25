<?php

namespace RedJasmine\Order\Application\Services\Handlers;

use Illuminate\Support\Str;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;
use RedJasmine\Support\Application\CommandHandler;


abstract class AbstractOrderCommandHandler extends CommandHandler
{

    protected ?string $pipelinesConfigKeyPrefix = 'red-jasmine.order.pipelines';

    protected ?Order $aggregate = null;

    public function __construct(protected OrderRepositoryInterface $orderRepository)
    {
        parent::__construct();
    }


    protected function find(int $id) : Order
    {
        $order = $this->orderRepository->find($id);
        $order->setOperator($this->getOperator());
        $this->setAggregate($order);
        return $order;

    }
}
