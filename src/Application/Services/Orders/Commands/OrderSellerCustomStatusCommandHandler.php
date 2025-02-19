<?php

namespace RedJasmine\Order\Application\Services\Orders\Commands;

use RedJasmine\Support\Exceptions\AbstractException;
use Throwable;

class OrderSellerCustomStatusCommandHandler extends AbstractOrderCommandHandler
{


    /**
     * @param OrderSellerCustomStatusCommand $command
     * @return void
     * @throws AbstractException
     * @throws Throwable
     */
    public function handle(OrderSellerCustomStatusCommand $command) : void
    {
        $this->beginDatabaseTransaction();

        try {
            $order = $this->find($command->id);


            $order->setSellerCustomStatus($command->sellerCustomStatus, $command->orderProductId);

            $this->service->repository->update($order);

            $this->commitDatabaseTransaction();
        } catch (AbstractException $exception) {
            $this->rollBackDatabaseTransaction();
            throw  $exception;
        } catch (Throwable $throwable) {
            $this->rollBackDatabaseTransaction();
            throw  $throwable;
        }

    }
}
