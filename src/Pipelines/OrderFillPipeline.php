<?php

namespace RedJasmine\Order\Pipelines;

use Closure;
use RedJasmine\Order\DataTransferObjects\OrderDTO;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Support\Helpers\Json\Json;

class OrderFillPipeline
{

    public function handle(Order $order, Closure $next)
    {

        $this->fillOrder($order);
        $order->products->each(function ($orderProduct) use ($order) {
            $this->fillOrderProduct($order, $orderProduct);
        });
        return $next($order);
    }


    public function fillOrder(Order $order) : void
    {

        /**
         *
         * @var $orderDTO OrderDTO
         */
        $orderDTO = $order->getDTO();

        $order->title                = $orderDTO->title;
        $order->order_type           = $orderDTO->orderType;
        $order->shipping_type        = $orderDTO->shippingType;
        $order->source               = $orderDTO->source;
        $order->order_status         = $orderDTO->orderStatus;
        $order->shipping_status      = $orderDTO->shippingStatus;
        $order->payment_status       = $orderDTO->paymentStatus;
        $order->refund_status        = $orderDTO->refundStatus;
        $order->rate_status          = $orderDTO->rateStatus ?? null;
        $order->freight_amount       = $orderDTO->freightAmount;
        $order->discount_amount      = $orderDTO->discountAmount;
        $order->client_type          = $parameters['client_type'] ?? null;
        $order->client_ip            = $parameters['client_ip'] ?? null;
        $order->channel_type         = $parameters['channel_type'] ?? null;
        $order->channel_id           = $parameters['channel_id'] ?? null;
        $order->store_type           = $orderDTO->store?->type;
        $order->store_id             = $orderDTO->store?->id;
        $order->guide                = $orderDTO->guide;
        $order->email                = $orderDTO->email;
        $order->password             = $orderDTO->password;
        $order->info->seller_remarks = $orderDTO->sellerRemarks;
        $order->info->seller_message = $orderDTO->sellerMessage;
        $order->info->buyer_remarks  = $orderDTO->buyerRemarks;
        $order->info->buyer_message  = $orderDTO->buyerMessage;
        $order->info->seller_extends = $orderDTO->sellerExtends;
        $order->info->buyer_extends  = $orderDTO->buyerExtends;
        $order->info->other_extends  = $orderDTO->otherExtends;


        dd($order);
    }

    public function fillOrderProduct(Order $order, OrderProduct $orderProduct) : void
    {

        $parameters                         = $orderProduct->getParameters();
        $orderProduct->order_status         = $order->order_status;
        $orderProduct->image                = $parameters['image'] ?? null;
        $orderProduct->category_id          = $parameters['category_id'] ?? null;
        $orderProduct->seller_category_id   = $parameters['seller_category_id'] ?? null;
        $orderProduct->outer_iid            = $parameters['outer_iid'] ?? null;
        $orderProduct->outer_sku_id         = $parameters['outer_sku_id'] ?? null;
        $orderProduct->shipping_status      = $parameters['shipping_status'] ?? null;
        $orderProduct->payment_status       = $parameters['payment_status'] ?? null;
        $orderProduct->refund_status        = $parameters['refund_status'] ?? null;
        $orderProduct->rate_status          = $parameters['rate_status'] ?? null;
        $orderProduct->info->seller_remarks = $parameters['info']['seller_remarks'] ?? null;
        $orderProduct->info->seller_message = $parameters['info']['seller_message'] ?? null;
        $orderProduct->info->buyer_remarks  = $parameters['info']['buyer_remarks'] ?? null;
        $orderProduct->info->buyer_message  = $parameters['info']['buyer_message'] ?? null;
        $orderProduct->info->seller_extends = Json::toArray($parameters['info']['seller_extends'] ?? null);
        $orderProduct->info->buyer_extends  = Json::toArray($parameters['info']['buyer_extends'] ?? null);
        $orderProduct->info->other_extends  = Json::toArray($parameters['info']['other_extends'] ?? null);
        $orderProduct->info->tools          = Json::toArray($parameters['info']['tools'] ?? null);
    }
}
