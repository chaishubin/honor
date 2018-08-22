<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manager', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nickname',100)->comment('昵称');
            $table->string('account',100)->comment('账户');
            $table->string('password',32)->comment('密码');
            $table->string('access_token',200)->nullable()->comment('登录的token');
            $table->tinyInteger('role')->comment('角色');
            $table->string('note',400)->nullable()->comment('备注');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `manager` COMMENT '管理员表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manager');
    }
}
