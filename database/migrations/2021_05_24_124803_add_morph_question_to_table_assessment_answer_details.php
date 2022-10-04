<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMorphQuestionToTableAssessmentAnswerDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_answer_details', function (Blueprint $table) {
            $table->nullableMorphs('res_question', 'mor_assessment_question_answers_res_question_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment_answer_details', function (Blueprint $table) {
            $table->dropMorphs('res_question');            
        });
    }
}
