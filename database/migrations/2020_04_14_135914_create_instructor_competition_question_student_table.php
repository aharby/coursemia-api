<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstructorCompetitionQuestionStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instructor_competition_question_student', function (Blueprint $table) {
            $table->unsignedBigInteger('exam_id')->index();
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');

            $table->unsignedBigInteger('exam_question_id')->index();
            $table->foreign('exam_question_id')->references('id')->on('exam_questions')->onDelete('cascade');

            $table->unsignedBigInteger('student_id')->index();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');


            $table->tinyInteger('is_correct_answer')->default(0);

            $table->primary(['exam_id', 'exam_question_id', 'student_id'],'comp_q_student_exam_id_exam_question_id_student_id');

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
        Schema::dropIfExists('instructor_competition_question_student');
    }
}
