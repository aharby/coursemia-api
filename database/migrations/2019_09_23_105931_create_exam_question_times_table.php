<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamQuestionTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_question_times', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug',20);

            $table->unsignedBigInteger('exam_question_id')->nullable()->index();
            $table->foreign('exam_question_id')->references('id')->on('exam_questions')->onDelete('SET NULL');


            $table->morphs('question_table');

            $table->unsignedBigInteger('exam_id')->nullable()->index();
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('SET NULL');

            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('SET NULL');

            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();

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
        Schema::dropIfExists('exam_question_times');
    }
}
