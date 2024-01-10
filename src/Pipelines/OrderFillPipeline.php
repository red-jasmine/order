<?php

namespace RedJasmine\Order\Pipelines;

use Closure;
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
        $parameters                  = $order->getParameters();
        $order->title                = $parameters['title'] ?? '';
        $order->order_type           = $parameters['order_type'] ?? null;
        $order->shipping_type        = $parameters['shipping_type'] ?? null;
        $order->source               = $parameters['source'] ?? null;
        $order->order_status         = $parameters['order_status'] ?? null;
        $order->shipping_status      = $parameters['shipping_status'] ?? null;
        $order->payment_status       = $parameters['payment_status'] ?? null;
        $order->refund_status        = $parameters['refund_status'] ?? null;
        $order->rate_status          = $parameters['rate_status'] ?? null;
        $order->freight_amount       = $parameters['freight_amount'] ?? 0;
        $order->discount_amount      = $parameters['discount_amount'] ?? 0;
        $order->client_type          = $parameters['client_type'] ?? null;
        $order->client_ip            = $parameters['client_ip'] ?? null;
        $order->channel_type         = $parameters['channel_type'] ?? null;
        $order->channel_id           = $parameters['channel_id'] ?? null;
        $order->store_type           = $parameters['store_type'] ?? null;
        $order->store_id             = $parameters['store_id'] ?? null;
        $order->guide_type           = $parameters['guide_type'] ?? null;
        $order->guide_id             = $parameters['guide_id'] ?? null;
        $order->email                = $parameters['email'] ?? null;
        $order->password             = $parameters['password'] ?? null;
        $order->info->seller_remarks = $parameters['info']['seller_remarks'] ?? null;
        $order->info->seller_message = $parameters['info']['seller_message'] ?? null;
        $order->info->buyer_remarks  = $parameters['info']['buyer_remarks'] ?? null;
        $order->info->buyer_message  = $parameters['info']['buyer_message'] ?? null;
        $order->info->seller_extends = Json::toArray($parameters['info']['seller_extends'] ?? null);
        $order->info->buyer_extends  = Json::toArray($parameters['info']['buyer_extends'] ?? null);
        $order->info->other_extends  = Json::toArray($parameters['info']['other_extends'] ?? null);

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
