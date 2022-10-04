<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCorrectOptionIdToGeneralExamQuestionQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_exam_question_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('general_exam_correct_option_id')->nullable()->index('general_exam_c_option_question_fr');
            $table->foreign('general_exam_correct_option_id', 'general_exam_c_option_question_ref_fr')->references('id')->on('general_exam_options')->onDelete('SET NULL');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_exam_question_questions', function (Blueprint $table) {
            $table->dropForeign('general_exam_c_option_question_ref_fr');
            $table->dropColumn('general_exam_correct_option_id');
        });
    }
}
