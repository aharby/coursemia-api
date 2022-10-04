<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreparedGeneralExamQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepared_general_exam_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('difficulty_level_id')
                ->index()
                ->nullable();
            $table->string('question_type', 20);

            $table->morphs('questionable', 'general_questionable_type');

            $table->unsignedBigInteger('subject_id')
                ->nullable()
                ->index();

            $table->unsignedBigInteger('subject_format_subject_id')
                ->nullable()
                ->index();

            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            $table->foreign('subject_format_subject_id', 'prepare_g_exam_questions_s_f_s_id_foreign')->references('id')->on('subject_format_subject')->onDelete('SET NULL');

            $table->foreign('difficulty_level_id')->references('id')->on('options')->onDelete('SET NULL');

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
        Schema::dropIfExists('prepared_general_exam_questions');
    }
}
