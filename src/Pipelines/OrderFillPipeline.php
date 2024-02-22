<?php

namespace RedJasmine\Order\Pipelines;

use Closure;
use JetBrains\PhpStorm\NoReturn;
use RedJasmine\Order\DataTransferObjects\OrderDTO;
use RedJasmine\Order\DataTransferObjects\OrderProductDTO;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Models\OrderProductInfo;
use RedJasmine\Support\Helpers\Json\Json;

class OrderFillPipeline
{

    public function handle(Order $order, Closure $next)
    {
        /**
         *
         * @var $orderDTO OrderDTO
         */
        $orderDTO = $order->getDTO();
        $this->fillOrder($order, $orderDTO);
        $orderDTO->products->each(function ($orderProductDTO) use ($order, $orderDTO) {
            $orderProduct = new OrderProduct();
            $orderProduct->setRelation('info', new OrderProductInfo());
            $orderProduct->setDTO($orderProductDTO);
            $this->fillOrderProduct($order, $orderProduct, $orderDTO, $orderProductDTO);
            $order->products->add($orderProduct);
        });
        return $next($order);
    }


    /**
     * @param Order    $order
     * @param OrderDTO $orderDTO
     *
     * @return void
     */
    public function fillOrder(Order $order, OrderDTO $orderDTO) : void
    {

        $order->seller               = $orderDTO->seller;
        $order->buyer                = $orderDTO->buyer;
        $order->title                = $orderDTO->title;
        $order->order_type           = $orderDTO->orderType;
        $order->shipping_type            = $orderDTO->shippingType;
        $order->source               = $orderDTO->source;
        $order->order_status         = $orderDTO->orderStatus;
        $order->shipping_status          = $orderDTO->shippingStatus;
        $order->payment_status       = $orderDTO->paymentStatus;
        $order->refund_status        = $orderDTO->refundStatus;
        $order->rate_status          = $orderDTO->rateStatus ?? null;
        $order->freight_amount       = $orderDTO->freightAmount;
        $order->discount_amount      = $orderDTO->discountAmount;
        $order->notifiable           = $orderDTO->notifiable;
        $order->password             = $orderDTO->password;
        $order->client_type          = $orderDTO->clientType;
        $order->client_ip            = $orderDTO->clientIp;
        $order->store_type           = $orderDTO->store?->type;
        $order->store_id             = $orderDTO->store?->id;
        $order->guide                = $orderDTO->guide;
        $order->channel_type         = $orderDTO->channel?->type;
        $order->channel_id           = $orderDTO->channel?->id;
        $order->info->seller_remarks = $orderDTO->info?->sellerRemarks;
        $order->info->seller_message = $orderDTO->info?->sellerMessage;
        $order->info->buyer_remarks  = $orderDTO->info?->buyerRemarks;
        $order->info->buyer_message  = $orderDTO->info?->buyerMessage;
        $order->info->seller_extends = $orderDTO->info?->sellerExtends;
        $order->info->buyer_extends  = $orderDTO->info?->buyerExtends;
        $order->info->other_extends  = $orderDTO->info?->otherExtends;
        $order->info->tools          = $orderDTO->info?->tools;

    }

    public function fillOrderProduct(Order $order, OrderProduct $orderProduct, OrderDTO $orderDTO, OrderProductDTO $orderProductDTO) : void
    {
        $orderProduct->order_status         = $orderProductDTO->orderStatus ?? $orderDTO->orderStatus;
        $orderProduct->order_product_type   = $orderProductDTO->orderProductType;
        $orderProduct->shipping_type            = $orderProductDTO->shippingType;
        $orderProduct->product_type         = $orderProductDTO->productType;
        $orderProduct->product_id           = $orderProductDTO->productId;
        $orderProduct->sku_id               = $orderProductDTO->skuId;
        $orderProduct->title                = $orderProductDTO->title;
        $orderProduct->sku_name             = $orderProductDTO->skuName;
        $orderProduct->price                = $orderProductDTO->price;
        $orderProduct->num                  = $orderProductDTO->num;
        $orderProduct->image                = $orderProductDTO->image;
        $orderProduct->category_id          = $orderProductDTO->categoryId;
        $orderProduct->seller_category_id   = $orderProductDTO->sellerCategoryId;
        $orderProduct->outer_id             = $orderProductDTO->outerId;
        $orderProduct->outer_sku_id         = $orderProductDTO->outerSkuId;
        $orderProduct->shipping_status          = $orderProductDTO->shippingStatus;
        $orderProduct->payment_status       = $orderProductDTO->paymentStatus;
        $orderProduct->refund_status        = $orderProductDTO->refundStatus;
        $orderProduct->rate_status          = $orderProductDTO->rateStatus;
        $orderProduct->info->seller_remarks = $orderProductDTO->info?->sellerRemarks;
        $orderProduct->info->seller_message = $orderProductDTO->info?->sellerMessage;
        $orderProduct->info->buyer_remarks  = $orderProductDTO->info?->buyerRemarks;
        $orderProduct->info->buyer_message  = $orderProductDTO->info?->buyerMessage;
        $orderProduct->info->seller_extends = $orderProductDTO->info?->sellerExtends;
        $orderProduct->info->buyer_extends  = $orderProductDTO->info?->buyerExtends;
        $orderProduct->info->other_extends  = $orderProductDTO->info?->otherExtends;
        $orderProduct->info->tools          = $orderProductDTO->info?->tools;
    }
}
