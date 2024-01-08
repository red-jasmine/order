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
    | 订单管道
    |--------------------------------------------------------------------------
    | 订单的一些操作会经过这些管道进行处理
    |
    */
    'pipelines' => [
        'creation' => [
            \RedJasmine\Order\Services\Orders\Pipelines\OrderFillPipeline::class,
            \RedJasmine\Order\Services\Orders\Pipelines\OrderCalculatePipeline::class,
            \RedJasmine\Order\Services\Orders\Pipelines\OrderValidatePipeline::class,
            \RedJasmine\Order\Services\Orders\Pipelines\OrderAddressPipeline::class,
        ],
    ],
    'actions'   => [
        'pay' => \RedJasmine\Order\Services\Orders\OrderPayAction::class,
    ],


];
