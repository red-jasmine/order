<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use RedJasmine\Ecommerce\Domain\Models\Enums\ProductTypeEnum;
use RedJasmine\Ecommerce\Domain\Models\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domain\Models\Enums\OrderStatusEnum;
use RedJasmine\Order\Domain\Models\Enums\PaymentStatusEnum;
use RedJasmine\Order\Domain\Models\Enums\RateStatusEnum;
use RedJasmine\Order\Domain\Models\Enums\RefundStatusEnum;
use RedJasmine\Order\Domain\Models\Enums\SettlementStatusEnum;
use RedJasmine\Order\Domain\Models\Enums\ShippingStatusEnum;

return new class extends Migration {
    public function up() : void
    {
        Schema::create(config('red-jasmine-order.tables.prefix', 'jasmine_') . 'order_products', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('商品单号');
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->string('seller_type', 32)->comment('卖家类型');
            $table->unsignedBigInteger('seller_id')->comment('卖家ID');
            $table->string('buyer_type', 32)->comment('买家类型');
            $table->unsignedBigInteger('buyer_id')->comment('买家类型');

            $table->string('order_product_type', 32)->comment(ProductTypeEnum::comments('订单商品类型'));
            $table->string('shipping_type', 32)->comment(ShippingTypeEnum::comments('发货类型'));
            $table->string('product_type', 32)->comment('商品源类型');
            $table->unsignedBigInteger('product_id')->comment('商品ID');
            $table->unsignedBigInteger('sku_id')->default(0)->comment('规格ID');
            $table->string('title')->comment('商品标题');
            $table->string('sku_name')->nullable()->comment('规格名称');
            $table->string('image')->nullable()->comment('图片');
            $table->unsignedBigInteger('category_id')->default(0)->comment('类目ID');
            $table->unsignedBigInteger('brand_id')->default(0)->comment('品牌ID');
            $table->unsignedBigInteger('product_group_id')->default(0)->comment('商品分组ID');
            $table->string('outer_product_id', 64)->nullable()->comment('外部商品编码');
            $table->string('outer_sku_id', 64)->nullable()->comment('外部规格编码');
            $table->string('barcode', 64)->nullable()->comment('条形码');
            $table->unsignedBigInteger('unit_quantity')->default(1)->comment('单位数量');
            $table->string('unit')->nullable()->comment('单位');
            $table->unsignedBigInteger('num')->default(0)->comment('数量');
            $table->decimal('price', 12)->default(0)->comment('价格');
            $table->decimal('cost_price', 12)->default(0)->comment('成本价格');
            $table->decimal('cost_amount', 12)->default(0)->comment('成本金额');
            // 金额类
            $table->decimal('product_amount', 12)->default(0)->comment('商品金额'); // =  price * num
            $table->decimal('tax_amount', 12)->default(0)->comment('税费金额');
            $table->decimal('discount_amount', 12)->default(0)->comment('优惠金额');
            $table->decimal('payable_amount', 12)->default(0)->comment('应付金额');
            $table->decimal('payment_amount', 12)->default(0)->comment('实付金额');
            $table->decimal('divided_discount_amount')->default(0)->comment('分摊优惠金额');
            $table->decimal('divided_payment_amount', 12)->default(0)->comment('分摊后实付金额');
            $table->decimal('refund_amount', 12)->default(0)->comment('退款金额');
            $table->decimal('commission_amount', 12)->default(0)->comment('佣金');
            // 状态
            $table->string('order_status', 32)->comment(OrderStatusEnum::comments('订单状态'));
            $table->string('payment_status', 32)->nullable()->comment(PaymentStatusEnum::comments('付款状态'));
            $table->string('shipping_status', 32)->nullable()->comment(ShippingStatusEnum::comments('发货状态'));
            $table->string('refund_status', 32)->nullable()->comment(RefundStatusEnum::comments('退款状态'));
            $table->string('rate_status', 32)->nullable()->comment(RateStatusEnum::comments('评价状态'));
            $table->string('settlement_status', 32)->nullable()->comment(SettlementStatusEnum::comments('结算状态'));
            $table->string('seller_custom_status', 32)->nullable()->comment('卖家自定义状态');
            $table->string('abnormal_status', 32)->nullable()->comment('异常状态');
            // 自动确认时间
            $table->unsignedBigInteger('progress')->nullable()->comment('进度');
            $table->unsignedBigInteger('progress_total')->nullable()->comment('进度总数');
            $table->unsignedBigInteger('gift_point')->default(0)->comment('赠送积分');

            $table->string('warehouse_code', 32)->nullable()->comment('仓库编码');
            $table->timestamp('expiration_date')->nullable()->comment('过期时间');

            $table->timestamp('created_time')->nullable()->comment('创建时间');
            $table->timestamp('payment_time')->nullable()->comment('付款时间');
            $table->timestamp('close_time')->nullable()->comment('关闭时间');
            $table->timestamp('shipping_time')->nullable()->comment('发货时间');
            $table->timestamp('collect_time')->nullable()->comment('揽收时间');
            $table->timestamp('dispatch_time')->nullable()->comment('派送时间');
            $table->timestamp('signed_time')->nullable()->comment('签收时间');
            $table->timestamp('confirm_time')->nullable()->comment('确认时间');
            $table->timestamp('refund_time')->nullable()->comment('退款时间');
            $table->timestamp('rate_time')->nullable()->comment('评价时间');
            $table->timestamp('settlement_time')->nullable()->comment('结算时间');
            $table->string('outer_order_product_id', 64)->nullable()->comment('外部商品单号');

            $table->unsignedBigInteger('refund_id')->nullable()->comment('最后退款单ID');
            // 供应商
            $table->unsignedBigInteger('batch_no')->default(0)->comment('批次号');
            $table->unsignedBigInteger('version')->default(0)->comment('版本');
            $table->nullableMorphs('creator');
            $table->nullableMorphs('updater');
            $table->timestamps();
            $table->softDeletes();
            $table->index('order_id', 'idx_order_id');
            $table->index([ 'seller_id', 'seller_type', 'order_id' ], 'idx_seller');
            $table->index([ 'buyer_id', 'buyer_type', 'order_id' ], 'idx_buyer');
            $table->comment('订单-商品表');
        });
    }

    public function down() : void
    {
        Schema::dropIfExists(config('red-jasmine-order.tables.prefix', 'jasmine_') . 'order_products');
    }
};
