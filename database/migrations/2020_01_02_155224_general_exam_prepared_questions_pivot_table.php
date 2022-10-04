<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GeneralExamPreparedQuestionsPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_prepared_questions_pivot', function (Blueprint $table) {
            $table->unsignedBigInteger('general_exam_id')
                ->index();

            $table->unsignedBigInteger('prepared_general_exam_question_id')
                ->index('prepared_general_index');

            $table->foreign('general_exam_id', 'general_exam_id_foreign')
                ->references('id')
                ->on('general_exams')
                ->onDelete('cascade');

            $table->foreign('prepared_general_exam_question_id', 'p_q_g_e_foreign')->references('id')->on('prepared_general_exam_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_exam_prepared_questions_pivot');
    }
}
