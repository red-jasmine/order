<?php

namespace RedJasmine\Order\Http\Buyer\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \RedJasmine\Order\Models\OrderInfo */
class OrderInfoResource extends JsonResource
{
    public function toArray(Request $request) : array
    {
        return [
            'id'             => $this->id,
            'seller_remarks' => $this->seller_remarks,
            'seller_message' => $this->seller_message,
            'buyer_remarks'  => $this->buyer_remarks,
            'buyer_message'  => $this->buyer_message,
            'seller_extends' => $this->seller_extends,
            'buyer_extends'  => $this->buyer_extends,
            'other_extends'  => $this->other_extends,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
