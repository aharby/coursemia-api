<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFieldsOfGeneralQuizStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_quiz_students', function (Blueprint $table) {
            $table->renameColumn('result', 'score_percentage');
            $table->renameColumn('finished_time', 'finish_at');
            $table->dateTime('start_at')->nullable();
            $table->float('score')->nullable();
            $table->boolean('is_reviewed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_quiz_students', function (Blueprint $table) {
            $table->renameColumn('score_percentage', 'result');
            $table->renameColumn('finish_at', 'finished_time');
            $table->dropColumn('start_at');
            $table->dropColumn('score');
            $table->dropColumn('is_reviewed');

        });
    }
}
