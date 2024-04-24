<?php

namespace RedJasmine\Order\Application\Services\Handlers;

use RedJasmine\Order\Application\Services\CommandHandler;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Repositories\OrderRepositoryInterface;


abstract class AbstractOrderCommandHandler extends CommandHandler
{

    protected ?Order $model = null;

    public function __construct(protected OrderRepositoryInterface $orderRepository)
    {
    }


    protected function find(int $id) : Order
    {
        $order = $this->orderRepository->find($id);
        $order->setOperator($this->getOperator());
        $this->setModel($order);
        return $order;

    }
}
