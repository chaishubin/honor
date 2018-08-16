<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHospitalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospital', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',128)->comment('医院名称');
            $table->integer('district_id')->comment('地区id');
            $table->string('district_address',200)->nullable()->comment('地区名称');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `hospital` COMMENT '医院表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hospital');
    }
}
