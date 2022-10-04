<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreparePracticeQuestionStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepare_practice_question_student', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained('prepare_exam_questions');
            $table->unsignedBigInteger('prepare_exam_question_id');
            $table->foreign('prepare_exam_question_id', 'practice_question_prepare_question_id_foreign')
                ->references('id')->on('prepare_exam_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prepare_practice_question_student');
    }
}
