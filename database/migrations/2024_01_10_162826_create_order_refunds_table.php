<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() : void
    {
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('退款单号');
            $table->unsignedBigInteger('order_id')->comment('订单号');
            $table->unsignedBigInteger('order_product_id')->comment('订单商品单号');
            $table->string('seller_type')->comment('卖家类型');
            $table->unsignedBigInteger('seller_id')->comment('卖家ID');
            $table->string('buyer_type')->comment('买家类型');
            $table->unsignedBigInteger('buyer_id')->comment('买家类型');
            $table->string('shipping_type', 30)->comment('发货类型');
            $table->string('order_product_type', 30)->comment('订单商品类型');
            $table->string('title')->comment('商品标题');
            $table->string('sku_name')->nullable()->comment('SKU名称');
            $table->string('image')->nullable()->comment('图片');
            $table->string('product_type', 30)->comment('商品多态类型');
            $table->unsignedBigInteger('product_id')->comment('商品ID');
            $table->unsignedBigInteger('sku_id')->default(0)->comment('规格ID');
            $table->unsignedBigInteger('category_id')->nullable()->comment('类目ID');
            $table->unsignedBigInteger('seller_category_id')->nullable()->comment('店内分类编号');
            $table->string('outer_id', 64)->nullable()->comment('商品外部编码');
            $table->string('outer_sku_id', 64)->nullable()->comment('SKU外部编码');
            $table->string('barcode', 64)->nullable()->comment('条形码');
            $table->unsignedBigInteger('num')->default(0)->comment('数量');
            $table->decimal('price', 12)->default(0)->comment('价格');
            $table->decimal('cost_price', 12)->default(0)->comment('成本价');
            $table->decimal('amount', 12)->default(0)->comment('商品金额');
            $table->decimal('tax_amount', 12)->default(0)->comment('税费');
            $table->decimal('discount_amount', 12)->default(0)->comment('商品优惠');
            $table->decimal('payment_amount', 12)->default(0)->comment('付款金额');
            $table->decimal('divided_payment_amount', 12)->default(0)->comment('分摊后实际付款金额');


            $table->string('phase')->comment('阶段');
            $table->string('refund_status')->comment('退款状态');
            $table->string('refund_type')->comment('退款类型');
            $table->decimal('freight_amount', 12)->default(0)->comment('运费');
            $table->decimal('refund_amount', 12)->default(0)->comment('退款金额');
            $table->unsignedTinyInteger('has_good_return')->default(0)->comment('是否需要退货');
            $table->string('good_status')->nullable()->comment('货物状态');

            $table->string('reason')->nullable()->comment('原因');
            $table->string('description')->nullable()->comment('描述');
            $table->json('images')->nullable()->comment('图片');

            $table->timestamp('created_time')->nullable()->comment('创建时间');
            $table->timestamp('end_time')->nullable()->comment('完结时间');

            $table->string('arbitrate_status')->nullable()->comment('仲裁状态');
            $table->string('arbitrate_result')->nullable()->comment('仲裁结果');
            $table->timestamp('arbitrate_time')->nullable()->comment('时间');
            $table->string('arbitrate_handler_type')->nullable()->comment('仲裁者类型');
            $table->unsignedBigInteger('arbitrate_handler_id')->nullable()->comment('仲裁者ID');

            $table->string('remarks')->nullable()->comment('备注');
            $table->json('extends')->nullable()->comment('扩展');
            $table->nullableMorphs('creator'); // 创建人
            $table->nullableMorphs('updater'); // 更新人
            $table->unsignedBigInteger('version')->nullable()->comment('版本');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('订单表');
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('order_refunds');
    }
};
