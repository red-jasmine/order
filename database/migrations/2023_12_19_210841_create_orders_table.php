<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() : void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('订单号');
            $table->string('seller_type', 32)->comment('卖家类型');
            $table->unsignedBigInteger('seller_id')->comment('卖家ID');
            $table->string('seller_nickname')->nullable()->comment('卖家昵称');
            $table->string('buyer_type', 32)->comment('买家类型');
            $table->unsignedBigInteger('buyer_id')->comment('买家类型');
            $table->string('buyer_nickname')->nullable()->comment('买家昵称');
            $table->string('title')->nullable()->comment('标题');
            $table->string('order_type', 32)->comment('订单类型');
            $table->string('shipping_type', 32)->comment('发货类型');
            $table->string('source', 30)->nullable()->comment('来源');   // 普通 、活动、
            $table->string('order_status')->comment('订单状态');
            $table->string('payment_status', 32)->nullable()->comment('付款状态');
            $table->string('shipping_status', 32)->nullable()->comment('发货状态');
            $table->string('refund_status', 32)->nullable()->comment('退款状态');
            $table->string('rate_status', 32)->nullable()->comment('评价状态');
            $table->string('seller_custom_status', 32)->nullable()->comment('卖家自定义状态');
            $table->decimal('total_product_amount', 12)->default(0)->comment('总商品金额');
            $table->decimal('total_cost_amount', 12)->default(0)->comment('总成本金额');
            $table->decimal('total_payable_amount', 12)->default(0)->comment('商品应付总金额');
            $table->decimal('freight_amount', 12)->default(0)->comment('运费');
            $table->decimal('discount_amount', 12)->default(0)->comment('订单优惠');
            $table->decimal('payable_amount', 12)->default(0)->comment('应付金额');
            $table->decimal('payment_amount', 12)->default(0)->comment('实付金额');
            $table->decimal('refund_amount', 12)->default(0)->comment('退款金额');
            $table->decimal('commission_amount', 12)->default(0)->comment('佣金');


            $table->timestamp('created_time')->nullable()->comment('创建时间');
            $table->timestamp('payment_time')->nullable()->comment('付款时间');
            $table->timestamp('close_time')->nullable()->comment('关闭时间');
            $table->timestamp('shipping_time')->nullable()->comment('发货时间');
            $table->timestamp('collect_time')->nullable()->comment('揽收时间');
            $table->timestamp('dispatch_time')->nullable()->comment('派送时间');
            $table->timestamp('signed_time')->nullable()->comment('签收时间');
            $table->timestamp('end_time')->nullable()->comment('确认时间');
            $table->timestamp('refund_time')->nullable()->comment('退款时间');
            $table->timestamp('rate_time')->nullable()->comment('评价时间');
            $table->string('client_type', 32)->nullable()->comment('客户端');
            $table->string('client_ip', 32)->nullable()->comment('IP');
            $table->string('channel_type', 32)->nullable()->comment('渠道类型');
            $table->unsignedBigInteger('channel_id')->nullable()->comment('渠道ID');
            $table->string('store_type', 32)->nullable()->comment('门店类型');
            $table->unsignedBigInteger('store_id')->nullable()->comment('门店ID');
            $table->string('guide_type', 32)->nullable()->comment('导购类型');
            $table->unsignedBigInteger('guide_id')->nullable()->comment('导购ID');
            $table->string('contact')->nullable()->comment('联系方式');
            $table->string('password')->nullable()->comment('查询密码');
            $table->string('payment_type', 32)->nullable()->comment('支付类型');
            $table->unsignedBigInteger('payment_id')->nullable()->comment('支付单号');
            $table->string('payment_channel')->nullable()->comment('支付渠道');
            $table->unsignedTinyInteger('is_seller_delete')->default(0)->comment('卖家删除');
            $table->unsignedTinyInteger('is_buyer_delete')->default(0)->comment('买家删除');
            $table->string('outer_order_id', 64)->nullable()->comment('外部订单号');
            $table->string('cancel_reason')->nullable()->comment('取消原因');
            $table->nullableMorphs('creator'); // 创建人
            $table->nullableMorphs('updater'); // 更新人
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
