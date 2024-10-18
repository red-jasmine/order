<?php


return [


    'tables' => [
        'prefix' => 'jasmine_', // 表前缀
    ],


    /*
    |--------------------------------------------------------------------------
    | 订单流程
    |--------------------------------------------------------------------------
    | 订单流程 如 标准、预售、团购、
    |
    */
    'flows'          => [
        'standard' => \RedJasmine\Order\Domain\Strategies\OrderStandardFlow::class,
        'presale'  => \RedJasmine\Order\Domain\Strategies\OrderPresaleFlow::class,
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
