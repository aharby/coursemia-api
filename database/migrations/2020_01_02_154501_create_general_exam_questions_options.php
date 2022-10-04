<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamQuestionsOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_questions_options', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('general_exam_option_id')->nullable()->index('general_exam_option_question_fr');
            $table->foreign('general_exam_option_id', 'general_exam_option_question_ref_fr')->references('id')->on('general_exam_options')->onDelete('SET NULL');

            $table->unsignedBigInteger('general_exam_question_question_id')->nullable()->index('general_exam_question_option_fr');
            $table->foreign('general_exam_question_question_id', 'general_exam_question_option_ref_fr')->references('id')->on('general_exam_question_questions')->onDelete('SET NULL');

            $table->unsignedBigInteger('general_exam_question_id')->index();
            $table->foreign('general_exam_question_id')->references('id')->on('general_exam_questions')->onDelete('CASCADE');

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
        Schema::dropIfExists('general_exam_questions_options');
    }
}
