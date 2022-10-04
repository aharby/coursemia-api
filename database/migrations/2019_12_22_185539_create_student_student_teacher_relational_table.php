<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentStudentTeacherRelationalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_student_teacher', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('student_teacher_id')->index();
            $table->unsignedBigInteger('student_id')->index();

            $table->foreign('student_teacher_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->string('status')->nullable()->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_student_teacher');
    }
}
