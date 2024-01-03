<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() : void
    {
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary()->comment('订单ID');

            $table->string('contacts', 30)->nullable()->comment('联系人');
            $table->string('mobile')->nullable()->comment('手机');
            $table->string('country', 20)->nullable()->comment('国家');
            $table->string('province', 20)->nullable()->comment('省份');
            $table->string('city', 30)->nullable()->comment('城市');
            $table->string('district', 40)->nullable()->comment('区县');
            $table->string('street', 50)->nullable()->comment('乡镇街道');
            $table->unsignedBigInteger('country_id')->nullable()->comment('国家ID');
            $table->unsignedBigInteger('province_id')->nullable()->comment('省份ID');
            $table->unsignedBigInteger('city_id')->nullable()->comment('城市ID');
            $table->unsignedBigInteger('district_id')->nullable()->comment('区县ID');
            $table->unsignedBigInteger('street_id')->nullable()->comment('乡镇街道ID');
            $table->string('address')->nullable()->comment('详细地址');
            $table->string('zip_code', 10)->nullable()->comment('邮政编码');
            $table->decimal('long', 13, 10)->nullable()->comment('经度');
            $table->decimal('lat', 13, 10)->nullable()->comment('纬度');

            $table->json('extends')->nullable()->comment('扩展字段');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('订单-地址表');
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('order_addresses');
    }
};
