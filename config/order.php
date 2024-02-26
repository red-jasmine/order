<?php


return [
    //

    /*
    |--------------------------------------------------------------------------
    | 订单 站内来源
    |--------------------------------------------------------------------------
    | 配置的订单站内来源  如 购物车、直购、活动、分销、
    |
    */
    'sources'        => [

    ],
    /*
    |--------------------------------------------------------------------------
    | 订单操作
    |--------------------------------------------------------------------------
    |
    |
    */
    'actions'        => [
        'order'  => [
            'create'            => \RedJasmine\Order\Actions\OrderCreateAction::class,
            'paid'              => \RedJasmine\Order\Actions\OrderPaidAction::class,
            'paying'            => \RedJasmine\Order\Actions\OrderPayingAction::class,
            'cancel'            => \RedJasmine\Order\Actions\OrderCancelAction::class,
            'confirm'           => \RedJasmine\Order\Actions\OrderConfirmAction::class,
            'virtualShipping'   => \RedJasmine\Order\Actions\Shipping\OrderVirtualShippingAction::class,
            'logisticsShipping' => \RedJasmine\Order\Actions\Shipping\OrderLogisticsShippingAction::class,
            'cardKeyShipping'   => \RedJasmine\Order\Actions\Shipping\OrderCardKeyShippingAction::class,
            'refundCreate'      => \RedJasmine\Order\Actions\Refunds\RefundCreateAction::class,
        ],
        'refund' => [
            'create'           => \RedJasmine\Order\Actions\Refunds\RefundCreateAction::class,
            'agree'            => \RedJasmine\Order\Actions\Refunds\RefundAgreeAction::class,
            'refuse'           => \RedJasmine\Order\Actions\Refunds\RefundRefuseAction::class,
            'cancel'           => \RedJasmine\Order\Actions\Refunds\RefundCancelAction::class,
            'agreeReturnGoods' => \RedJasmine\Order\Actions\Refunds\RefundAgreeReturnGoodsAction::class,
            'returnGoods'      => \RedJasmine\Order\Actions\Refunds\RefundReturnGoodsAction::class,
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | 操作管道
    |--------------------------------------------------------------------------
    | 操作会经过这些管道进行依次处理
    |
    */
    'pipelines'      => [
        'order'  => [
            'create' => [
                \RedJasmine\Order\Pipelines\OrderFillPipeline::class,
                \RedJasmine\Order\Pipelines\OrderCalculatePipeline::class,
                \RedJasmine\Order\Pipelines\OrderValidatePipeline::class,
                \RedJasmine\Order\Pipelines\OrderAddressPipeline::class,
            ],
        ],
        'refund' => [
            'agree' => [],
        ],

    ],


    /*
    |--------------------------------------------------------------------------
    | 退款配置
    |--------------------------------------------------------------------------
    | 操作会经过这些管道进行依次处理
    |
    */
    'refund_reasons' => [
        '拍错/多拍',
        '快递问题',
        '未收到货',
        '地址错误',
        '不喜欢/不想要',
        '商品与描述不符',
        '其他',
    ],


];
