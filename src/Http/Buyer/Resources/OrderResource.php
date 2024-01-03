<?php

namespace RedJasmine\Order\Http\Buyer\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \RedJasmine\Order\Models\Order */
class OrderResource extends JsonResource
{
    protected function formatDateTime($date)
    {
        return $date->format('Y-m-d H:i:s');
    }


    public function toArray(Request $request) : array
    {
        return [
            'id'               => $this->id,
            'seller_type'      => $this->seller_type,
            'seller_id'        => $this->seller_id,
            'seller_nickname'  => $this->seller_nickname,
            'buyer_type'       => $this->buyer_type,
            'buyer_id'         => $this->buyer_id,
            'buyer_nickname'   => $this->buyer_nickname,
            'title'            => $this->title,
            'order_type'       => $this->order_type,
            'shipping_type'    => $this->shipping_type,
            'source'           => $this->source,
            'order_status'     => $this->order_status,
            'shipping_status'  => $this->shipping_status,
            'payment_status'   => $this->payment_status,
            'refund_status'    => $this->refund_status,
            'rate_status'      => $this->rate_status,
            'total_amount'     => $this->total_amount,
            'freight_amount'   => $this->freight_amount,
            'discount_amount'  => $this->discount_amount,
            'payment_amount'   => $this->payment_amount,
            'refund_amount'    => $this->refund_amount,
            // 'cost_amount'      => $this->cost_amount,
            'created_time'     => $this->created_time,
            'payment_time'     => $this->payment_time,
            'close_time'       => $this->close_time,
            'consign_time'     => $this->consign_time,
            'collect_time'     => $this->collect_time,
            'dispatch_time'    => $this->dispatch_time,
            'signed_time'      => $this->signed_time,
            'end_time'         => $this->end_time,
            'refund_time'      => $this->refund_time,
            'rate_time'        => $this->rate_time,
            // 'is_seller_delete' => $this->is_seller_delete,
            'is_buyer_delete'  => $this->is_buyer_delete,
            'client_type'      => $this->client_type,
            'client_ip'        => $this->client_ip,
            'channel_id'       => $this->channel_id,
            'channel_type'     => $this->channel_type,
            'store_id'         => $this->store_id,
            'store_type'       => $this->store_type,
            'guide_id'         => $this->guide_id,
            'guide_type'       => $this->guide_type,
            'email'            => $this->email,
            'password'         => $this->password,
            'creator_id'       => $this->creator_id,
            'creator_type'     => $this->creator_type,
            'updater_id'       => $this->updater_id,
            'updater_type'     => $this->updater_type,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'products_count'   => $this->products_count,
            'info'             => new OrderInfoResource($this->whenLoaded('info')),
            'address'          => new OrderAddressResource($this->whenLoaded('address')),
            'products'         => OrderProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
