<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamChallengesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_challenges', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('exam_id')->nullable()->index();
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('SET NULL');

            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('SET NULL');

            $table->unsignedBigInteger('related_exam_id')->nullable()->index();
            $table->foreign('related_exam_id')->references('id')->on('exams')->onDelete('SET NULL');

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
        Schema::dropIfExists('exam_challenges');
    }
}
