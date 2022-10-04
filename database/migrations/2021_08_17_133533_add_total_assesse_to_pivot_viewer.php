<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalAssesseToPivotViewer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_result_viewers', function (Blueprint $table) {
            $table->integer('total_assesses_count')->default(0);
            $table->integer('assessed_assesses_count')->default(0);
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
            $table->dropColumn('total_assesses_count');
            $table->dropColumn('assessed_assesses_count');
        });
    }
}
