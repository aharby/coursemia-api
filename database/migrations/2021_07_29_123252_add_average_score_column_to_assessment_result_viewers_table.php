<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAverageScoreColumnToAssessmentResultViewersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_result_viewers', function (Blueprint $table) {
            $table->float('average_score')->default(0.0)->after("user_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment_result_viewers', function (Blueprint $table) {
            $table->dropColumn("average_score");
        });
    }
}
