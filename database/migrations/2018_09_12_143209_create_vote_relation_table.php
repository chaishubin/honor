<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoteRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vote_relation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('candidate_id')->comment('候选人id');
            $table->integer('voters_id')->comment('投票人id');
            $table->tinyInteger('voters_type')->comment('投票人类型，1大众，2专家');
            $table->integer('vote_time')->nullable()->comment('投票时间');
            $table->integer('voters_ip')->nullable()->comment('投票人ip');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `vote_relation` COMMENT '投票-选举人关系表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vote_relation');
    }
}
