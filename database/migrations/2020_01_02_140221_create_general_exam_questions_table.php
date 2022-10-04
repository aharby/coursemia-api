<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('question')->nullable();
            $table->longText('description')->nullable();

            $table->unsignedBigInteger('difficulty_level_id')
                ->index()
                ->nullable();

            $table->string('question_type', 50);

            $table->unsignedBigInteger('general_exam_id')
                ->index();

            $table->unsignedBigInteger('subject_format_subject_id')
                ->nullable()
                ->index();

            $table->boolean('is_true')->nullable();
            $table->timestamps();

            $table->foreign('general_exam_id')->references('id')->on('general_exams')->onDelete('CASCADE');

            $table->foreign('subject_format_subject_id', 'g_exam_questions_s_f_s_id_foreign')->references('id')->on('subject_format_subject')->onDelete('SET NULL');

            $table->foreign('difficulty_level_id')->references('id')->on('options')->onDelete('SET NULL');

            $table->morphs('questionable', 'general_exam_questionable_type');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_exam_questions');
    }
}
