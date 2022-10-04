<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentAnswerDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_answer_details', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('option','assessment_answers_option_id'); //answer id and answer table
            $table->unsignedBigInteger('assessment_question_id')->nullable()->index();
            $table->foreign('assessment_question_id' , 'question_ref_assess_questions')->references('id')->on('assessment_questions')->onDelete('SET NULL');
            $table->unsignedBigInteger('assessment_answer_id')->nullable()->index();
            $table->foreign('assessment_answer_id')->references('id')->on('assessment_answers')->onDelete('SET NULL');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id' )->references('id')->on('users')->onDelete('SET NULL');
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
        Schema::dropIfExists('assessment_answer_details');
    }
}
