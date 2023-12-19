<?php

namespace RedJasmine\Order\Services\Orders;


use Illuminate\Support\Collection;
use RedJasmine\Order\Contracts\ProductInterface;
use RedJasmine\Order\Enums\Orders\OrderTypeEnums;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnums;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnums;
use RedJasmine\Order\OrderService;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Traits\Services\ServiceExtends;


class OrderCreatorService
{
    use ServiceExtends;


    /**
     * @var array|Collection|ProductInterface[]
     */
    protected array|Collection $products = [];

    protected UserInterface  $owner;
    protected UserInterface  $seller;
    protected ?UserInterface $guide   = null;
    protected ?UserInterface $channel = null;
    protected ?UserInterface $store   = null;

    protected array $validators = [];


    protected OrderTypeEnums $orderType;

    protected ShippingTypeEnums $shippingType;

    public function __construct(protected OrderService $service)
    {

    }


    // 验证
    public function validate()
    {


    }


    public function sum()
    {



        $totalAmount = 0;
        foreach ($this->products as $product) {
            // 单品优惠 TODO
            $productAmount = bcmul($product->getNum(), $product->getPrice(), 2);
            $totalAmount   = bcadd($totalAmount, $productAmount, 2);
        }
        // TODO 总价类 满减


        dd($totalAmount);

    }

    public function create()
    {

        // 订单验证
        // 生成单号
        // 计算订单金额
        //

    }


    public function addProduct(ProductInterface $product)
    {
        $this->products[] = $product;
        return $this;
    }

    public function getSeller() : UserInterface
    {
        return $this->seller;
    }

    public function setSeller(UserInterface $seller) : OrderCreatorService
    {
        $this->seller = $seller;
        return $this;
    }


}
