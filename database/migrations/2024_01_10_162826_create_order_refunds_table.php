<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() : void
    {
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_product_id');
            $table->string('seller_type')->comment('卖家 类型');
            $table->unsignedBigInteger('seller_id')->comment('卖家 ID');
            $table->string('seller_nickname')->nullable()->comment('卖家昵称');
            $table->string('buyer_type')->comment('买家类型');
            $table->unsignedBigInteger('buyer_id')->comment('买家类型');
            $table->string('buyer_nickname')->nullable()->comment('买家昵称');

            $table->string('title')->nullable()->comment('标题');
            $table->string('image')->nullable()->comment('图片');
            $table->string('product_type', 30)->comment('商品类型');
            $table->unsignedBigInteger('product_id')->comment('商品ID');


            $table->nullableMorphs('creator'); // 创建人
            $table->nullableMorphs('updater'); // 更新人
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
