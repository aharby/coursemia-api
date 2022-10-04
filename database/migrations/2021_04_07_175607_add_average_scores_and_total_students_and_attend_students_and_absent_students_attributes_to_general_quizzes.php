<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAverageScoresAndTotalStudentsAndAttendStudentsAndAbsentStudentsAttributesToGeneralQuizzes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_quizzes', function (Blueprint $table) {
            $table->float("average_scores")->default(0);
            $table->integer("total_students")->default(0);
            $table->integer("attend_students")->default(0);
            $table->integer("absent_students")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_quizzes', function (Blueprint $table) {
            $table->dropColumn("average_scores");
            $table->dropColumn("total_students");
            $table->dropColumn("attend_students");
            $table->dropColumn("absent_students");
        });
    }
}
