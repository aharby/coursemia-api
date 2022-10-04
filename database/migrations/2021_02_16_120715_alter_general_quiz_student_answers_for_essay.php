<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AlterGeneralQuizStudentAnswersForEssay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_quiz_student_answers', function (Blueprint $table) {
            $table->text('answer_text')->change();
            $table->float('score')->default(0);
            $table->boolean('is_reviewed')->default(true);
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
            $table->string('answer_text')->change();
            $table->dropColumn('score');
            $table->dropColumn('is_reviewed');
        });
    }
}
