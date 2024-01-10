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
    'sources'   => [

    ],
    /*
    |--------------------------------------------------------------------------
    | 订单操作
    |--------------------------------------------------------------------------
    |
    |
    */
    'actions'   => [
        'paying' => \RedJasmine\Order\Actions\OrderPayingAction::class,
        'create' => \RedJasmine\Order\Actions\OrderCreateAction::class,
        'paid'   => \RedJasmine\Order\Actions\OrderPaidAction::class,
        'cancel' => \RedJasmine\Order\Actions\OrderCancelAction::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | 操作管道
    |--------------------------------------------------------------------------
    | 操作会经过这些管道进行依次处理
    |
    */
    'pipelines' => [
        'create' => [
            \RedJasmine\Order\Pipelines\OrderFillPipeline::class,
            \RedJasmine\Order\Pipelines\OrderCalculatePipeline::class,
            \RedJasmine\Order\Pipelines\OrderValidatePipeline::class,
            \RedJasmine\Order\Pipelines\OrderAddressPipeline::class,
        ],
    ],


];
