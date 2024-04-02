<?php

namespace RedJasmine\Order\Application\Order\Mappers;

use RedJasmine\Order\Application\Order\Data\OrderData;
use RedJasmine\Order\Application\Order\Data\OrderProductData;
use RedJasmine\Order\Domain\Order\Models\Order;
use RedJasmine\Order\Domain\Order\Models\OrderInfo;
use RedJasmine\Order\Domain\Order\Models\OrderProduct;
use RedJasmine\Order\Domain\Order\Models\OrderProductInfo;


class OrderMapper
{


    public function fromData(OrderData $orderData) : Order
    {
        $order = new  Order();
        $order->setRelation('info', new OrderInfo());
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
            $order->setRelation('address', app(OrderAddressMapper::class)->formData($orderData->address));
        }

        $orderData->products->each(function ($orderProductData) use ($order, $orderData) {
            $orderProduct = new OrderProduct();
            $orderProduct->setRelation('info', new OrderProductInfo());
            // TODO 转换
            $this->fillOrderProduct($order, $orderProduct, $orderData, $orderProductData);
            $order->addProduct($orderProduct);
        });

        return $order;
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
