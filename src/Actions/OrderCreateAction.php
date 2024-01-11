<?php

namespace RedJasmine\Order\Actions;


use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RedJasmine\Order\DataTransferObjects\OrderDTO;
use RedJasmine\Order\Models\Order;

use RedJasmine\Order\Models\OrderInfo;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Models\OrderProductInfo;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Exceptions\AbstractException;
use RedJasmine\Support\Helpers\ID\Snowflake;
use Throwable;


class OrderCreateAction extends AbstractOrderAction
{

    protected ?string $pipelinesConfigKey = 'red-jasmine.order.pipelines.create';


    protected array $validators = [];


    /**
     * @var Order|null
     */
    protected ?Order $order = null;

    public function __construct()
    {
        $this->initOrder();
    }

    public function initOrder() : void
    {
        $this->order = new Order();
        $this->order->setRelation('info', new OrderInfo());
        $this->order->setRelation('products', collect());
    }


    /**
     * @return Order
     * @throws AbstractException
     * @throws Throwable
     */
    public function create() : Order
    {
        $this->order->creator = $this->service->getOperator();
        try {
            DB::beginTransaction();
            // 数据验证
            $order = $this->pipelines($this->order)->then(function (Order $order) {
                return $this->save($order);
            });
            DB::commit();
            return $order;
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
    }

    /**
     * @param Order $order
     *
     * @return Order
     * @throws Exception
     */
    protected function save(Order $order) : Order
    {
        $order->id = $this->buildID();
        $order->products->each(function ($product) use ($order) {
            $product->id           = $product->id ?? $this->buildID();
            $product->order_status = $order->order_status;
        });
        $order->save();
        $order->info()->save($order->info);
        $order->products()->saveMany($order->products);
        $order->products->each(function ($product) {
            $product->info()->save($product->info);
        });

        return $order;
    }


    /**
     * @param OrderDTO $orderDTO
     *
     * @return Order
     * @throws AbstractException
     * @throws Throwable
     */
    public function execute(OrderDTO $orderDTO) : Order
    {
        $this->order->setDTO($orderDTO);
        return $this->create();
    }

    /**
     * 生成订单ID
     * @return int
     * @throws Exception
     */
    public function buildID() : int
    {
        return Snowflake::getInstance()->nextId();
    }

}
