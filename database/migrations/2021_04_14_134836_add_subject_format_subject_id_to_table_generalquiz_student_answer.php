<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubjectFormatSubjectIdToTableGeneralquizStudentAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_quiz_student_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_format_subject_id')->nullable();
            // $table->foreign('subject_format_subject_id')->references('id')->on('subject_format_subject')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_quiz_student_answers', function (Blueprint $table) {
            // $table->dropForeign(['subject_format_subject_id']);
            $table->dropColumn('subject_format_subject_id');

        });
    }
}
