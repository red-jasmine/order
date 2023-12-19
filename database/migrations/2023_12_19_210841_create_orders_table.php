<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() : void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->morphs('seller');  // 卖家
            $table->morphs('buyer');  // 买家
            $table->string('title')->nullable()->comment('标题');
            // 类型 如 普通订单、拍卖单、
            $table->string('type', 30)->comment('订单类型');
            $table->string('shipping_type', 30)->comment('发货类型');
            // 普通 、活动、
            $table->string('source_type', 30)->comment('来源类型');
            // 状态
            $table->string('order_status')->comment('订单状态');

            $table->string('shipping_status', 30)->nullable()->comment('发货状态');
            $table->string('payment_status', 30)->nullable()->comment('付款状态');
            $table->string('refund_status', 30)->nullable()->comment('退款状态');
            $table->string('rate_status', 30)->nullable()->comment('评价状态');
            // 金额
            $table->unsignedBigInteger('num')->default(0)->comment('总数量');

            $table->decimal('product_amount', 16)->default(0)->comment('商品金额');
            $table->decimal('tax_fee', 16)->default(0)->comment('税费');
            $table->decimal('freight_fee', 16)->default(0)->comment('运费');
            $table->decimal('adjust_amount', 16)->default(0)->comment('调整金额');
            $table->decimal('discount_amount', 16)->default(0)->comment('订单优惠');
            $table->decimal('amount', 16)->default(0)->comment('金额');
            $table->decimal('payment_amount', 16)->default(0)->comment('实付金额');
            $table->decimal('refund_amount', 16)->default(0)->comment('退款金额');

            $table->nullableMorphs('channel'); // 渠道
            $table->nullableMorphs('store'); // 门店
            $table->nullableMorphs('guide');// 导购

            // 时间
            $table->timestamp('created_time')->nullable()->comment('创建时间');
            $table->timestamp('payment_time')->nullable()->comment('付款时间');
            $table->timestamp('close_time')->nullable()->comment('关闭时间');
            $table->timestamp('consign_time')->nullable()->comment('发货时间');
            $table->timestamp('collect_time')->nullable()->comment('揽收时间');
            $table->timestamp('dispatch_time')->nullable()->comment('派送时间');
            $table->timestamp('signed_time')->nullable()->comment('签收时间');
            $table->timestamp('end_time')->nullable()->comment('确认时间');
            $table->timestamp('refund_time')->nullable()->comment('退款时间');
            $table->timestamp('rate_time')->nullable()->comment('评价时间');


            $table->nullableMorphs('creator'); // 创建人
            $table->nullableMorphs('updater'); // 更新人
            $table->unsignedTinyInteger('is_seller_delete')->default(0)->comment('卖家删除');
            $table->unsignedTinyInteger('is_buyer_delete')->default(0)->comment('买家删除');
            $table->string('client_type', 30)->comment('客户端');
            $table->string('client_ip', 30)->comment('IP');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('订单表');
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('orders');
    }
};
