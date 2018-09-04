<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDoctorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctor', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100)->comment('姓名');
            $table->tinyInteger('sex')->nullable()->comment('性别');
            $table->tinyInteger('age')->nullable()->comment('年龄');
            $table->tinyInteger('wanted_award')->comment('报名奖项');
            $table->tinyInteger('working_year')->nullable()->comment('工作年限');
            $table->integer('hospital_id')->comment('所属医院id');
            $table->string('hospital_name',100)->comment('所属医院名称');
            $table->string('department',100)->comment('所属科室');
            $table->integer('job_title')->nullable()->comment('专业职称');
            $table->string('phone_number',100)->comment('手机号');
            $table->string('medical_certificate_no',50)->nullable()->comment('医师资格证号');
            $table->string('email',100)->comment('邮箱');
            $table->string('full_face_photo',400)->comment('免冠照片');
            $table->json('doctor_other_info')->nullable()->comment('报名医生其他详细信息，比如医患故事、个人荣誉等等');
            $table->tinyInteger('status')->nullable()->default(1)->comment('报名状态,1待审核，2已通过，3未通过');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `doctor` COMMENT '医生表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctor');
    }
}
