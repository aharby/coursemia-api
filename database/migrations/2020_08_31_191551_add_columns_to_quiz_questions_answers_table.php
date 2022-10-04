<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToQuizQuestionsAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quiz_questions_answers', function (Blueprint $table) {
            $table->bigIncrements('id')->change();

            $table->unsignedInteger('question_grade')->nullable();
            $table->boolean('is_correct_option')->default(0);

            $table->unsignedBigInteger('quiz_id')->nullable()->index();
            $table->foreign('quiz_id')->references('id')
                ->on('quizzes')->onDelete('SET NULL');

            $table->unsignedBigInteger('option_id')->nullable()->index();
            $table->foreign('option_id')->references('id')
                ->on('quiz_questions_options')->onDelete('SET NULL');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quiz_questions_answers', function (Blueprint $table) {
            $table->dropForeign('option_id');
            $table->dropForeign('quiz_id');
            $table->dropColumn('option_id');
            $table->dropColumn('is_correct_option');
            $table->dropColumn('quiz_id');
            $table->dropColumn('question_grade');
        });
    }
}
