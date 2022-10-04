<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToStudentQuizTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_quiz', function (Blueprint $table) {

            $table->string('quiz_type')->nullable();
            $table->string('status')->nullable();

            $table->float('quiz_result')->nullable();



            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')
                ->on('students')->onDelete('SET NULL');

            $table->unsignedBigInteger('quiz_id')->nullable()->index();
            $table->foreign('quiz_id')->references('id')
                ->on('quizzes')->onDelete('SET NULL');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_quiz', function (Blueprint $table) {
            $table->dropColumn('quiz_type');
            $table->dropColumn('status');
            $table->dropColumn('quiz_result');
            $table->dropColumn('started_at');
            $table->dropColumn('finished_at');

            $table->dropForeign(['student_id']);
            $table->dropForeign(['quiz_id']);
            $table->dropColumn('student_id');
            $table->dropColumn('quiz_id');
        });
    }
}
