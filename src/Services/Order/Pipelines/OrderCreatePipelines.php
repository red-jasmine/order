<?php

namespace RedJasmine\Order\Services\Order\Pipelines;

use RedJasmine\Order\Models\OrderAddress;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Models\OrderProductInfo;
use RedJasmine\Order\Services\Order\Actions\OrderCreateAction;
use RedJasmine\Order\Services\Order\Data\OrderData;
use RedJasmine\Order\Services\Order\Data\OrderProductData;
use RedJasmine\Order\Services\Order\Enums\PaymentStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;
use RedJasmine\Support\Foundation\Service\Action;

/**
 * @property OrderCreateAction $action
 */
class OrderCreatePipelines
{

    /**
     * @param OrderCreateAction $action
     * @param \Closure          $next
     *
     * @return mixed
     */
    public function init(Action $action, \Closure $next)
    {
        return $next($action);
    }

    /**
     * @param OrderCreateAction $action
     * @param \Closure          $next
     *
     * @return mixed
     */
    public function validate(Action $action, \Closure $next)
    {

        return $next($action);
    }

    /**
     * @param OrderCreateAction $action
     * @param \Closure          $next
     *
     * @return mixed
     */
    public function fill(Action $action, \Closure $next)
    {


        // 开始填充 这部分应该放在 核心方法内
        /**
         * @var Order $order
         */
        $order     = $action->getModel();
        $orderData = $action->getData();

        $this->fillOrder($order, $orderData);
        $this->initStatus($order);
        $orderData->products->each(function ($orderProductData) use ($order, $orderData) {
            $orderProduct = new OrderProduct();
            $orderProduct->setRelation('info', new OrderProductInfo());
            $this->fillOrderProduct($order, $orderProduct, $orderData, $orderProductData);
            $order->products->add($orderProduct);
        });

        // 订单计算
        $this->calculate($order);

        $result = $next($action);
        return $result;
    }

    /**
     * @param OrderCreateAction $action
     * @param \Closure          $next
     *
     * @return mixed
     */
    public function handle(Action $action, \Closure $next)
    {
        return $next($action);
    }


    protected function calculate(Order $order) : void
    {
        // 统计商品金额
        $this->calculateProducts($order);
        // 汇总订单金额
        $this->calculateOrder($order);
        // 分摊订单数据
        $this->calculateDivideDiscount($order);
    }


    protected function calculateProducts(Order $order) : void
    {
        foreach ($order->products as $product) {
            // 商品总金额   < 0
            $product->product_amount = bcmul($product->num, $product->price, 2);
            // 成本金额
            $product->cost_amount = bcmul($product->num, $product->cost_price, 2);
            // 计算税费
            $product->tax_amount = bcadd($product->tax_amount, 0, 2);
            // 单品优惠
            $product->discount_amount = bcadd($product->discount_amount, 0, 2);
            // 应付金额  = 商品金额 + 税费 - 单品优惠

            $product->payable_amount = bcsub(bcadd($product->product_amount, $product->tax_amount, 2), $product->discount_amount, 2);

            // 实付金额 完成支付时
            $product->payment_amount = 0;

        }
    }


    protected function calculateOrder(Order $order) : void
    {
        // 商品金额
        $order->total_product_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->product_amount, 2);
        }, 0);
        // 商品成本
        $order->total_cost_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->cost_amount, 2);
        }, 0);
        // 商品应付
        $order->total_payable_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->payable_amount, 2);
        }, 0);

        // 邮费
        $order->freight_amount = bcadd($order->freight_amount, 0, 2);
        // 订单优惠
        $order->discount_amount = bcadd($order->discount_amount, 0, 2);

        // 订单应付金额 = 商品总应付金额 + 邮费 - 优惠
        $order->payable_amount = bcsub(bcadd($order->total_payable_amount, $order->freight_amount, 2), $order->discount_amount, 2);

    }

    /**
     * 计算分摊优惠
     * @return void
     */
    public function calculateDivideDiscount(Order $order)
    {
        $order->discount_amount;
        // 对商品进行排序 从小到大
        $products = $order->products->sortBy('product_amount')->values();
        // TODO
    }


    protected function initStatus(Order $order) : void
    {
        $order->order_status    = OrderStatusEnum::WAIT_BUYER_PAY;
        $order->payment_status  = null;
        $order->shipping_status = null;
        $order->refund_status   = null;
        $order->rate_status     = null;
    }

    public function fillOrder(Order $order, OrderData $orderData) : void
    {

        $order->seller               = $orderData->seller;
        $order->buyer                = $orderData->buyer;
        $order->title                = $orderData->title;
        $order->order_type           = $orderData->orderType;
        $order->shipping_type        = $orderData->shippingType;
        $order->source               = $orderData->source;
        $order->seller_custom_status = $orderData->sellerCustomStatus;
        $order->freight_amount       = $orderData->freightAmount;
        $order->discount_amount      = $orderData->discountAmount;
        $order->contact              = $orderData->contact;
        $order->password             = $orderData->password;
        $order->client_type          = $orderData->clientType;
        $order->client_ip            = $orderData->clientIp;
        $order->guide                = $orderData->guide;
        $order->store_type           = $orderData->store?->type;
        $order->store_id             = $orderData->store?->id;
        $order->channel_type         = $orderData->channel?->type;
        $order->channel_id           = $orderData->channel?->id;
        $order->outer_order_id       = $orderData->outerOrderId;
        $order->info->seller_remarks = $orderData->info?->sellerRemarks;
        $order->info->seller_message = $orderData->info?->sellerMessage;
        $order->info->buyer_remarks  = $orderData->info?->buyerRemarks;
        $order->info->buyer_message  = $orderData->info?->buyerMessage;
        $order->info->seller_extends = $orderData->info?->sellerExtends;
        $order->info->buyer_extends  = $orderData->info?->buyerExtends;
        $order->info->other_extends  = $orderData->info?->otherExtends;
        $order->info->tools          = $orderData->info?->tools;


        if ($orderData->address) {
            $order->setRelation('address', OrderAddress::make($orderData->address->toArray()));
        }

    }

    public function fillOrderProduct(Order $order, OrderProduct $orderProduct, OrderData $orderData, OrderProductData $orderProductData) : void
    {

        $orderProduct->seller                 = $order->seller;
        $orderProduct->buyer                  = $order->buyer;
        $orderProduct->order_product_type     = $orderProductData->orderProductType;
        $orderProduct->shipping_type          = $orderProductData->shippingType;
        $orderProduct->product_type           = $orderProductData->productType;
        $orderProduct->product_id             = $orderProductData->productId;
        $orderProduct->sku_id                 = $orderProductData->skuId;
        $orderProduct->title                  = $orderProductData->title;
        $orderProduct->sku_name               = $orderProductData->skuName;
        $orderProduct->price                  = $orderProductData->price;
        $orderProduct->num                    = $orderProductData->num;
        $orderProduct->image                  = $orderProductData->image;
        $orderProduct->category_id            = $orderProductData->categoryId;
        $orderProduct->seller_category_id     = $orderProductData->sellerCategoryId;
        $orderProduct->outer_id               = $orderProductData->outerId;
        $orderProduct->outer_sku_id           = $orderProductData->outerSkuId;
        $orderProduct->seller_custom_status   = $orderProductData->sellerCustomStatus;
        $orderProduct->outer_order_product_id = $orderProductData->outerOrderProductId;
        $orderProduct->info->seller_remarks   = $orderProductData->info?->sellerRemarks;
        $orderProduct->info->seller_message   = $orderProductData->info?->sellerMessage;
        $orderProduct->info->buyer_remarks    = $orderProductData->info?->buyerRemarks;
        $orderProduct->info->buyer_message    = $orderProductData->info?->buyerMessage;
        $orderProduct->info->seller_extends   = $orderProductData->info?->sellerExtends;
        $orderProduct->info->buyer_extends    = $orderProductData->info?->buyerExtends;
        $orderProduct->info->other_extends    = $orderProductData->info?->otherExtends;
        $orderProduct->info->tools            = $orderProductData->info?->tools;
    }


}
