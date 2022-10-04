<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamQuestionQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_question_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('question');
            $table->unsignedBigInteger('general_exam_question_id')
                ->index();

            $table->foreign('general_exam_question_id')->references('id')->on('general_exam_questions')->onDelete('CASCADE');

            $table->timestamps();

            //There is another migration To add correct option id to this table after options table migration :D
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_exam_question_questions');
    }
}
