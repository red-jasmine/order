<?php

namespace RedJasmine\Order\Application\Http\Buyer\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \RedJasmine\Order\Models\OrderProduct */
class OrderProductResource extends JsonResource
{
    public function toArray(Request $request) : array
    {
        return [
            'id'                     => $this->id,
            'shipping_type'          => $this->shipping_type,
            'title'                  => $this->title,
            'image'                  => $this->image,
            'product_type'           => $this->product_type,
            'product_id'             => $this->product_id,
            'sku_id'                 => $this->sku_id,
            'category_id'            => $this->category_id,
            'seller_category_id'     => $this->seller_category_id,
            'outer_iid'              => $this->outer_iid,
            'outer_sku_id'           => $this->outer_sku_id,
            'barcode'                => $this->barcode,
            'num'                    => $this->num,
            'price'                  => $this->price,
            'cost_price'             => $this->cost_price,
            'amount'                 => $this->amount,
            'tax_amount'             => $this->tax_amount,
            'discount_amount'        => $this->discount_amount,
            'payment_amount'         => $this->payment_amount,
            'divide_discount_amount' => $this->divide_discount_amount,
            'divided_payment_amount' => $this->divided_payment_amount,
            'refund_amount'          => $this->refund_amount,
            'cost_amount'            => $this->cost_amount,
            'order_status'           => $this->order_status,
            'shipping_status'        => $this->shipping_status,
            'payment_status'         => $this->payment_status,
            'refund_status'          => $this->refund_status,
            'rate_status'            => $this->rate_status,
            'progress'               => $this->progress,
            'progress_total'         => $this->progress_total,
            'warehouse_code'         => $this->warehouse_code,
            'refund_id'              => $this->refund_id,
            'created_time'           => $this->created_time,
            'payment_time'           => $this->payment_time,
            'close_time'             => $this->close_time,
            'consign_time'           => $this->consign_time,
            'collect_time'           => $this->collect_time,
            'dispatch_time'          => $this->dispatch_time,
            'signed_time'            => $this->signed_time,
            'end_time'               => $this->end_time,
            'refund_time'            => $this->refund_time,
            'rate_time'              => $this->rate_time,
            'creator_id'             => $this->creator_id,
            'creator_type'           => $this->creator_type,
            'updater_id'             => $this->updater_id,
            'updater_type'           => $this->updater_type,
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
            'oid'                    => $this->oid,
            'info'                   => new OrderProductInfoResource($this->whenLoaded('info')),
            'order'                  => new OrderResource($this->whenLoaded('order')),
        ];
    }
}
