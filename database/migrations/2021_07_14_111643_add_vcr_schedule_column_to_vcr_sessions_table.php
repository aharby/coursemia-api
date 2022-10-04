<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVcrScheduleColumnToVcrSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vcr_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger("vcr_schedule_id")
                ->nullable()
                ->after('course_id');

            $table->foreign('vcr_schedule_id')
                ->references('id')
                ->on('vcr_schedule_instructor')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vcr_sessions', function (Blueprint $table) {
            $table->dropForeign('vcr_schedule_id');
            $table->dropColumn('vcr_schedule_id');
        });
    }

}
