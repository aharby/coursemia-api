<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAverageTotalMarkToAssessorResultViewerAvgscoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessor_result_viewer_avgscore', function (Blueprint $table) {
            $table->float("average_total_mark")->default(0.0)->after("average_score");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessor_result_viewer_avgscore', function (Blueprint $table) {
            $table->dropColumn("average_total_mark");
        });
    }
}
