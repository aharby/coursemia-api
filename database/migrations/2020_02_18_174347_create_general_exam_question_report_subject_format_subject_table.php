<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamQuestionReportSubjectFormatSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_question_report_subject_format_subject', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('section_id')->nullable()->index('ge_question_report_child_section_id_fr');
            $table->foreign('section_id', 'ge_question_report_child_section_id_ref_fr')->references('id')->on('subject_format_subject')->onDelete('SET NULL');

            $table->unsignedBigInteger('section_parent_id')->nullable()->index('ge_question_report_parent_section_id_fr');
            $table->foreign('section_parent_id', 'ge_question_report_parent_section_id_ref_fr')->references('id')->on('subject_format_subject')->onDelete('SET NULL');

            $table->unsignedBigInteger('subject_id')->nullable()->index('ge_question_report_subject_id_fr');
            $table->foreign('subject_id', 'ge_question_report_subject_id_ref_fr')->references('id')->on('subjects')->onDelete('SET NULL');

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
        Schema::dropIfExists('general_exam_question_report_subject_format_subject');
    }
}
