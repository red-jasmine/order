<?php

use RedJasmine\Order\Services\Orders\Pipelines\OrderCreatePipeline;

return [
    //

    /*
    |--------------------------------------------------------------------------
    | 订单 站内来源
    |--------------------------------------------------------------------------
    | 配置的订单站内来源  如 购物车、直购、活动、分销、
    |
    */
    'sources'    => [

    ],


    // 订单验证器
    'validators' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | 订单管道
    |--------------------------------------------------------------------------
    | 订单的一些操作会经过这些管道进行处理
    |
    */
    'pipelines'  => [
        // 产品
        'product' => [
            \RedJasmine\Order\Services\Orders\Pipelines\Products\ProductPipeline::class
        ],
        'create'  => [
            OrderCreatePipeline::class
        ],
    ],
];
