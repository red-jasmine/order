<?php

namespace RedJasmine\Order\Enums\Orders;

use RedJasmine\Support\Helpers\Enums\EnumsHelper;

/**
 * 退款状态
 */
enum RefundStatusEnum: string
{

    use EnumsHelper;

    case  WAIT_SELLER_AGREE = 'wait_seller_agree';
    case  WAIT_BUYER_RETURN_GOODS = 'wait_buyer_return_goods';
    case  WAIT_SELLER_CONFIRM_GOODS = 'wait_seller_confirm_goods';
    case  SELLER_REFUSE_BUYER = 'seller_refuse_buyer';
    case  REFUND_SUCCESS = 'refund_success';
    case  REFUND_CLOSED = 'refund_closed';

    public static function labels() : array
    {
        return [

            self::WAIT_SELLER_AGREE->value         => '等待卖家同意', // 申请操作->
            self::WAIT_BUYER_RETURN_GOODS->value   => '等待买家退货', // 同意退货->
            self::WAIT_SELLER_CONFIRM_GOODS->value => '等待卖家确认收货', // 买家已发货->
            self::SELLER_REFUSE_BUYER->value       => '卖家拒绝退款', // 拒绝退款
            self::REFUND_SUCCESS->value            => '退款成功', // 同意退款->,卖家已发货->
            self::REFUND_CLOSED->value             => '退关关闭', // 买家取消->
        ];
    }
}
