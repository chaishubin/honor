<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistrictTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district', function (Blueprint $table) {
            $table->integer('id')->comment('id');
            $table->string('name',50)->comment('名称（国家、省、市、区）');
            $table->integer('parent_id')->comment('父id');
            $table->string('shortname',50)->comment('简称');
            $table->integer('leveltyp')->comment('级别（国家0，省1，市2，区3');
            $table->string('citycode',50)->comment('区域代码');
            $table->string('zipcode',50)->comment('邮编');
            $table->string('mergershortname',100)->comment('全称');
            $table->string('remarks',400)->comment('备注');
            $table->string('code',50);
            $table->timestamps();
            $table->primary('id');
        });

        DB::statement("ALTER TABLE `district` COMMENT '地区表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('district');
    }
}
