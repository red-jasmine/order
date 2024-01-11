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


    protected UserInterface $buyer;

    protected UserInterface $seller;

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
            $order = $this->pipelines($this->order, function (Order $order) {
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


    public function executeV2(OrderDTO $orderDTO)
    {
        $this->order->setData($orderDTO);
        $order = $this->pipelines($this->order, function (Order $order) {
           return $order;
        });

        dd($order);

    }

    /**
     * 创建订单
     *
     * @param UserInterface $seller
     * @param UserInterface $buyer
     * @param array         $orderParameters
     * @param Collection    $products
     *
     * @return Order
     * @throws AbstractException
     * @throws Throwable
     */
    public function execute(UserInterface $seller, UserInterface $buyer, array $orderParameters, Collection $products) : Order
    {
        //$this->setOrderParameters($orderParameters);
        $this->setBuyer($buyer)->setSeller($seller);
        $products->each(fn($product) => $this->addProduct($product));
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

    public function getOrder() : Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order) : OrderCreateAction
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param array|Collection|OrderProduct[] $products
     *
     * @return static
     * @throws Exception
     */
    public function setProducts(array|Collection $products) : static
    {
        $this->order->setRelation('products', []);
        foreach ($products as $product) {
            $this->addProduct($product);
        }
        return $this;
    }

    /**
     * 添加商品
     *
     * @param OrderProduct $product
     *
     * @return $this
     * @throws Exception
     */
    public function addProduct(OrderProduct $product) : static
    {
        if ($product->relationLoaded('info') === false) {
            $product->setRelation('info', new OrderProductInfo());
        }
        $this->order->products->add($product);
        return $this;
    }


    public function getSeller() : UserInterface
    {
        return $this->seller;
    }

    public function setSeller(UserInterface $seller) : OrderCreateAction
    {
        $this->seller        = $seller;
        $this->order->seller = $seller;
        return $this;
    }

    public function getBuyer() : UserInterface
    {
        return $this->buyer;
    }

    public function setBuyer(UserInterface $buyer) : OrderCreateAction
    {
        $this->buyer        = $buyer;
        $this->order->buyer = $buyer;
        return $this;
    }

}
