<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpertTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expert', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name','100')->comment('姓名');
            $table->string('phone_number',20)->comment('手机号');
            $table->tinyInteger('status')->comment('状态');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `expert` COMMENT '专家表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expert');
    }
}
