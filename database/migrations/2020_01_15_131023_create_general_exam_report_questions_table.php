<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamReportQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_report_questions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('total_answers')->nullable();
            $table->string('correct_answers')->nullable();
            $table->string('wrong_answers')->nullable();
            $table->string('difficulty_parameter')->nullable();
            $table->string('easy_parameter')->nullable();
            $table->string('stability_parameter')->nullable();
            $table->string('trust_parameter')->nullable();
            $table->string('preference_parameter')->nullable();

            $table->unsignedBigInteger('general_exam_id')->nullable()->index();
            $table->foreign('general_exam_id')->references('id')->on('general_exams')->onDelete('SET NULL');

            $table->unsignedBigInteger('general_exam_question_id')->nullable()->index();
            $table->foreign('general_exam_question_id')->references('id')->on('general_exam_questions')->onDelete('SET NULL');

            $table->unsignedBigInteger('general_exam_report_id')->nullable()->index();
            $table->foreign('general_exam_report_id')->references('id')->on('general_exam_reports')->onDelete('SET NULL');

            $table->unsignedBigInteger('subject_format_subject_id')
                ->nullable()
                ->index();

            $table->foreign('subject_format_subject_id', 'g_exam_report_questions_s_f_s_id_foreign')->references('id')->on('subject_format_subject')->onDelete('SET NULL');

            $table->boolean('is_reported')->default(0);
            $table->boolean('is_ignored')->default(0);
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
        Schema::dropIfExists('general_exam_report_questions');
    }
}
