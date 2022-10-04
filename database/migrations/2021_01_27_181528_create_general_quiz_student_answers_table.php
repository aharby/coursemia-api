<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralQuizStudentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_quiz_student_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->unsignedBigInteger('general_quiz_id')->nullable()->index();
            $table->foreign('general_quiz_id')->references('id')->on('general_quizzes')->onDelete('SET NULL');
            
            $table->unsignedBigInteger('general_quiz_question_id')->nullable()->index();
            $table->foreign('general_quiz_question_id')->references('id')->on('general_quiz_question_bank')->onDelete('SET NULL');

            $table->nullableMorphs('single_question', 'mor_quiz_question_answers_single_question_id'); //single morph in drag_drop match multi matching

            $table->nullableMorphs('option','quiz_student_answers_option_id'); //answer id and answer table

            $table->string('answer_text')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->string('time_to_solve')->nullable();

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
        Schema::dropIfExists('general_quiz_student_answers');
    }
}
