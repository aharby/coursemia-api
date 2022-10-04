<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddingTimeToSolveColumnToHotSpotQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('res_hot_spot_questions', function (Blueprint $table) {
            $table->decimal('time_to_solve',8,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('solve_column_to_hot_spot_questions', function (Blueprint $table) {
            $table->dropColumn('time_to_solve');
        });
    }
}
