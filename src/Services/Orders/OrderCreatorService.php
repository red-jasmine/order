<?php

namespace RedJasmine\Order\Services\Orders;


use Illuminate\Support\Collection;
use RedJasmine\Order\Contracts\ProductInterface;
use RedJasmine\Order\Enums\Orders\OrderTypeEnums;
use RedJasmine\Order\Enums\Orders\ShippingStatusEnums;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnums;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\OrderService;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Traits\Services\ServiceExtends;
use function Symfony\Component\Translation\t;


class OrderCreatorService
{
    use ServiceExtends;


    /**
     * @var array|Collection|ProductInterface[]|OrderProduct[]
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


    protected ?Order $order = null;

    public function __construct(protected OrderService $service)
    {

    }


    // 验证
    public function validate()
    {


    }

    public function calculate()
    {
        $this->initOrder();
        $this->calculateProducts();
        $this->calculateOrder();
    }


    protected function calculateProducts() : static
    {
        foreach ($this->products as $product) {
            // 商品金额
            $product->product_amount = bcmul($product->getNum(), $product->getPrice(), 2);
            // 成本金额
            $product->cost_amount = bcmul($product->getNum(), $product->getPrice(), 2);
            // 计算税费
            $product->tax_amount = bcadd($product->getTaxAmount(), 0, 2);
            // 单品优惠 TODO
            $product->discount_amount = bcadd($product->getDiscountAmount(), 0, 2);
            // 付款金额
            $product->payment_amount = bcsub(bcadd($product->product_amount, $product->tax_amount, 2), $product->discount_amount, 2);
        }

        return $this;

    }


    protected function calculateOrder() : static
    {
        // 计算商品金额
        $this->order->total_amount = $this->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->payment_amount, 2);
        }, 0);
        // 统计成本
        $this->order->cost_amount = $this->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->cost_amount, 2);
        }, 0);
        // 邮费
        $this->order->freight_amount = bcadd(0, 0, 2);
        // 订单优惠
        $this->order->discount_amount = bcadd(0, 0, 2);
        // 计算付款 金额 = 商品总金额 + 邮费 - 优惠
        $this->order->payment_amount = bcsub(bcadd($this->order->total_amount, $this->order->freight_amount, 2), $this->order->discount_amount, 2);
        // TODO 计算分摊
        return $this;
    }

    public function initOrder() : static
    {
        if ($this->order) {
            return $this->order;
        }
        $this->order = new Order();

        return $this;
    }

    public function create()
    {

        // 计算金额
        $this->calculate();
        // 订单验证
        $this->validate();


    }


    public function addProduct(ProductInterface $product) : static
    {
        $this->products   = collect($this->products);
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
