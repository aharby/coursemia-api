<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_question_answers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->nullableMorphs('option_table'); //answer id and answer table
            $table->string('answer_text')->nullable();

            $table->boolean('is_correct_answer')->default(0);

            $table->unsignedBigInteger('question_id')->nullable()->index();
            $table->foreign('question_id')->references('id')->on('exam_questions')->onDelete('SET NULL');

            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('SET NULL');

            $table->nullableMorphs('single_question', 'mor_exam_question_answers_single_question_id'); //single morph in drag_drop match multi matching

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
        Schema::dropIfExists('exam_question_answers');
    }
}
