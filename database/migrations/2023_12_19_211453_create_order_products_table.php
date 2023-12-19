<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() : void
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->unsignedTinyInteger('oid')->comment('订单ID');
            $table->morphs('seller');  // 卖家
            $table->morphs('buyer');  // 买家
            $table->string('title')->nullable()->comment('标题');
            $table->unsignedBigInteger('num')->default(0)->comment('数量');
            $table->decimal('price', 12)->default(0)->comment('价格');

            $table->decimal('product_amount', 16)->default(0)->comment('商品金额');

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
