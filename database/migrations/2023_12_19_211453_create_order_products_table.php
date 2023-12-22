<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() : void
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('oid')->comment('订单ID');
            // 商品信息
            $table->string('title')->nullable()->comment('标题');
            $table->string('image')->nullable()->comment('图片');
            $table->string('product_type', 30)->comment('商品类型');
            $table->unsignedBigInteger('product_id')->comment('商品ID');
            $table->unsignedBigInteger('sku_id')->default(0)->comment('规格ID');
            $table->unsignedBigInteger('category_id')->nullable()->comment('类目ID');
            $table->unsignedBigInteger('seller_category_id')->nullable()->comment('店内分类编号');
            $table->string('outer_iid', 64)->nullable()->comment('商品外部编码');
            $table->string('outer_sku_id', 64)->nullable()->comment('SKU外部编码');
            $table->string('barcode', 64)->nullable()->comment('条形码');

            $table->string('shipping_type', 30)->comment('发货类型');

            $table->unsignedBigInteger('num')->default(0)->comment('数量');
            $table->decimal('price', 12)->default(0)->comment('价格');
            $table->decimal('cost_price', 12)->default(0)->comment('成本价');
            $table->decimal('amount', 16)->default(0)->comment('商品金额');
            $table->decimal('tax_amount', 16)->default(0)->comment('税费');
            $table->decimal('discount_amount', 16)->default(0)->comment('商品优惠');
            $table->decimal('payment_amount', 16)->default(0)->comment('付款金额');
            $table->decimal('divide_discount_amount')->default(0)->comment('分摊优惠');
            $table->decimal('divided_payment_amount', 16)->default(0)->comment('分摊后实际付款金额');
            $table->decimal('refund_amount', 16)->default(0)->comment('退款金额');
            $table->decimal('cost_amount', 16)->default(0)->comment('成本金额');

            // 状态
            $table->string('order_status')->comment('状态');
            $table->string('shipping_status', 30)->nullable()->comment('发货状态');
            $table->string('payment_status', 30)->nullable()->comment('付款状态');
            $table->string('refund_status', 30)->nullable()->comment('退款状态');
            $table->string('rate_status', 30)->nullable()->comment('评价状态');

            // 发货
            $table->unsignedBigInteger('progress')->nullable()->comment('进度');
            $table->unsignedBigInteger('progress_total')->nullable()->comment('进度总数');
            $table->string('warehouse_code', 32)->nullable()->comment('仓库编码');
            $table->string('tools_label')->nullable()->comment('工具标签');

            // 退款
            $table->unsignedBigInteger('refund_id')->nullable()->comment('退款单ID');


            // 流程时间
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
            $table->timestamps();
            $table->softDeletes();
            $table->comment('订单-商品表');
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('order_products');
    }
};