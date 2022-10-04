<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralQuizStudentAnswerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // general_quiz_student_answers_question_table_type_question_table_id_index
        Schema::create('generalquiz_student_answer_details', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('option','quiz_answers_option_id'); //answer id and answer table
            $table->boolean('is_correct_answer')->default(0);
            $table->unsignedBigInteger('question_id')->nullable()->index();
            $table->foreign('question_id' , 'question_ref_g_q_questions')->references('id')->on('general_quiz_question_bank')->onDelete('SET NULL');
            $table->unsignedBigInteger('main_answer_id')->nullable()->index();
            $table->foreign('main_answer_id' , 'main_answer_ref_g_q_student_answers')->references('id')->on('general_quiz_student_answers')->onDelete('SET NULL');
            $table->nullableMorphs('single_question', 'quiz_answers_single_question_id'); //single morph in drag_drop match multi matching
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id' )->references('id')->on('users')->onDelete('SET NULL');

            
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
        Schema::dropIfExists('general_quiz_student_answer_details');
    }
}
