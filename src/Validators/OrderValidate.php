<?php

namespace RedJasmine\Order\Validators;


use Illuminate\Validation\Rules\Enum;
use RedJasmine\Order\Enums\Orders\OrderStatusEnum;
use RedJasmine\Order\Enums\Orders\OrderTypeEnum;
use RedJasmine\Order\Enums\Orders\PaymentStatusEnum;
use RedJasmine\Order\Enums\Orders\RefundStatusEnum;
use RedJasmine\Order\Enums\Orders\ShippingTypeEnum;

class OrderValidate
{


    public function rules() : array
    {
        $order   = [
            'seller_type'         => [ 'required', 'string', 'min:1', 'max:255' ],
            'seller_id'           => [ 'required', 'integer', 'min:0', 'max:18446744073709551615' ],
            'seller_nickname'     => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'buyer_type'          => [ 'required', 'string', 'min:1', 'max:255' ],
            'buyer_id'            => [ 'required', 'integer', 'min:0', 'max:18446744073709551615' ],
            'buyer_nickname'      => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'title'               => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'order_type'          => [ 'required', 'string', 'min:1', 'max:30' ],
            'shipping_type'       => [ 'required', 'string', 'min:1', 'max:30' ],
            'source'              => [ 'nullable', 'string', 'min:1', 'max:30' ],
            'order_status'        => [ 'required', 'string', 'min:1', 'max:255' ],
            'shipping_status'     => [ 'nullable', 'string', 'min:1', 'max:30' ],
            'payment_status'      => [ 'nullable', 'string', 'min:1', 'max:30' ],
            'refund_status'       => [ 'nullable', 'string', 'min:1', 'max:30' ],
            'rate_status'         => [ 'nullable', 'string', 'min:1', 'max:30' ],
            'total_amount'        => [ 'sometimes', 'numeric' ],
            'freight_amount'      => [ 'sometimes', 'numeric' ],
            'discount_amount'     => [ 'sometimes', 'numeric' ],
            'payment_amount'      => [ 'sometimes', 'numeric' ],
            'refund_amount'       => [ 'sometimes', 'numeric' ],
            'cost_amount'         => [ 'sometimes', 'numeric' ],
            'created_time'        => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'payment_time'        => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'close_time'          => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'consign_time'        => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'collect_time'        => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'dispatch_time'       => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'signed_time'         => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'end_time'            => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'refund_time'         => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'rate_time'           => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'is_seller_delete'    => [ 'sometimes', 'integer', 'min:0', 'max:255' ],
            'is_buyer_delete'     => [ 'sometimes', 'integer', 'min:0', 'max:255' ],
            'client_type'         => [ 'nullable', 'string', 'min:1', 'max:30' ],
            'client_ip'           => [ 'nullable', 'string', 'min:1', 'max:30' ],
            'channel_type'        => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'channel_id'          => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'store_type'          => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'store_id'            => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'guide_type'          => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'guide_id'            => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'email'               => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'password'            => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'creator_type'        => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'creator_id'          => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'updater_type'        => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'updater_id'          => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'info.seller_remarks' => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'info.seller_message' => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'info.buyer_remarks'  => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'info.buyer_message'  => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'info.seller_extends' => [ 'nullable', 'array' ],
            'info.buyer_extends'  => [ 'nullable', 'array' ],
            'info.other_extends'  => [ 'nullable', 'array' ]
        ];
        $product = [
            'shipping_type'          => [ 'required', 'string', 'min:1', 'max:30' ],
            'title'                  => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'image'                  => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'product_type'           => [ 'required', 'string', 'min:1', 'max:30' ],
            'product_id'             => [ 'required', 'integer', 'min:0', 'max:18446744073709551615' ],
            'sku_id'                 => [ 'required', 'integer', 'min:0', 'max:18446744073709551615' ],
            'category_id'            => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'seller_category_id'     => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'outer_iid'              => [ 'nullable', 'string', 'min:1', 'max:64' ],
            'outer_sku_id'           => [ 'nullable', 'string', 'min:1', 'max:64' ],
            'barcode'                => [ 'nullable', 'string', 'min:1', 'max:64' ],
            'num'                    => [ 'required', 'integer', 'min:0', 'max:18446744073709551615' ],
            'price'                  => [ 'required', 'numeric' ],
            'cost_price'             => [ 'sometimes', 'numeric' ],
            'amount'                 => [ 'required', 'numeric' ],
            'tax_amount'             => [ 'required', 'numeric' ],
            'discount_amount'        => [ 'required', 'numeric' ],
            'payment_amount'         => [ 'required', 'numeric' ],
            'divide_discount_amount' => [ 'sometimes', 'numeric' ],
            'divided_payment_amount' => [ 'sometimes', 'numeric' ],
            'refund_amount'          => [ 'sometimes', 'numeric' ],
            'cost_amount'            => [ 'sometimes', 'numeric' ],
            'order_status'           => [ 'required', 'string', 'min:1', 'max:255' ],
            'shipping_status'        => [ 'nullable', 'string', 'min:1', 'max:30' ],
            'payment_status'         => [ 'nullable', 'string', 'min:1', 'max:30' ],
            'refund_status'          => [ 'nullable', 'string', 'min:1', 'max:30' ],
            'rate_status'            => [ 'nullable', 'string', 'min:1', 'max:30' ],
            'progress'               => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'progress_total'         => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'warehouse_code'         => [ 'nullable', 'string', 'min:1', 'max:32' ],
            'refund_id'              => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'created_time'           => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'payment_time'           => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'close_time'             => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'consign_time'           => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'collect_time'           => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'dispatch_time'          => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'signed_time'            => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'end_time'               => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'refund_time'            => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'rate_time'              => [ 'nullable', 'date', 'after_or_equal:1970-01-01 00:00:01', 'before_or_equal:2038-01-19 03:14:07' ],
            'creator_type'           => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'creator_id'             => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'updater_type'           => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'updater_id'             => [ 'nullable', 'integer', 'min:0', 'max:18446744073709551615' ],
            'info.seller_remarks'    => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'info.seller_message'    => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'info.buyer_remarks'     => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'info.buyer_message'     => [ 'nullable', 'string', 'min:1', 'max:255' ],
            'info.seller_extends'    => [ 'nullable', 'array' ],
            'info.buyer_extends'     => [ 'nullable', 'array' ],
            'info.other_extends'     => [ 'nullable', 'array' ],
            'info.tools'             => [ 'nullable', 'array' ],
        ];
        $rules   = $order;
        foreach ($product as $key => $item) {
            $rules['products.*.' . $key] = $item;
        }
        return $rules;
    }
}
