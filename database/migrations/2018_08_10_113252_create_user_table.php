<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('phone_number',20)->comment('手机号');
            $table->string('access_token',200)->nullable()->comment('登录的token');
            $table->integer('reg_time')->comment('注册时间');
            $table->tinyInteger('status')->default(0)->comment('用户状态，默认0禁用，1启用');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `user` COMMENT '用户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
