<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamStudentAnswersDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_student_answers_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('general_exam_option_id')->nullable()->index('g_e_answers_details_option_id');
            $table->foreign('general_exam_option_id' , 'g_e_option_answers_details_ref_g_e_options')->references('id')->on('general_exam_options')->onDelete('SET NULL');

            $table->boolean('is_correct_answer')->default(0);

            $table->unsignedBigInteger('question_id')->nullable()->index();
            $table->foreign('question_id' , 'question_ref_g_e_questions')->references('id')->on('general_exam_questions')->onDelete('SET NULL');

            $table->unsignedBigInteger('main_answer_id')->nullable()->index();
            $table->foreign('main_answer_id' , 'main_answer_ref_g_e_student_answers')->references('id')->on('general_exam_student_answers')->onDelete('SET NULL');

            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id' )->references('id')->on('students')->onDelete('SET NULL');

            $table->unsignedBigInteger('single_question_id')->nullable()->index();
            $table->foreign('single_question_id' , 'single_question_ref_id_g_e_question_questions')->references('id')->on('general_exam_question_questions')->onDelete('SET NULL');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_exam_student_answers_details');
    }
}
