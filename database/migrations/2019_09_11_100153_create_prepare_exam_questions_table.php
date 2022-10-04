<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrepareExamQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepare_exam_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('difficulty_level',20)->nullable();
            $table->string('question_type',20);
            $table->decimal('time_to_solve',8,2)->nullable();
            $table->morphs('question_table','p_e_questions_q_table_type_question_table_id_index');

            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            $table->unsignedBigInteger('subject_format_subject_id')->nullable()->index();
            $table->foreign('subject_format_subject_id','prepare_exam_questions_s_f_s_id_foreign')->references('id')->on('subject_format_subject')->onDelete('SET NULL');

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
        Schema::dropIfExists('prepare_exam_questions');
    }
}
