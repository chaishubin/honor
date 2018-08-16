<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignupInfoReviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signup_info_review', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('审核人id');
            $table->integer('info_id')->comment('审核信息id');
            $table->tinyInteger('status')->comment('审核状态');
            $table->string('content',400)->comment('审核内容');
            $table->tinyInteger('review_way')->comment('审核方式，1单条审核，2批量审核');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `signup_info_review` COMMENT '报名信息审核表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('signup_info_review');
    }
}
