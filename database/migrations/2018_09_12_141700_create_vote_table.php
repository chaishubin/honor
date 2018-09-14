<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户id');
            $table->bigInteger('public_votes')->nullable()->comment('大众票数');
            $table->bigInteger('expert_votes')->nullable()->comment('专家票数');
            $table->bigInteger('score')->nullable()->comment('分数');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `vote` COMMENT '投票表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote');
    }
}
