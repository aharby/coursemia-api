<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToPrepareExamQuestionsAndPrepareGeneralExamQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prepare_exam_questions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('prepared_general_exam_questions', function (Blueprint $table) {
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
        Schema::table('prepare_exam_questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('prepared_general_exam_questions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
