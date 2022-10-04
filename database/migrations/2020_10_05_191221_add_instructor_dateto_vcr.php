<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstructorDatetoVcr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vcr_sessions', function (Blueprint $table) {
            $table->boolean('is_ended_by_instructor')->default(0)->after('time_to_end');
            $table->timestamp('instructor_end_time')->nullable()->after('time_to_end');
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
            $table->dropColumn('is_ended_by_instructor');
            $table->dropColumn('instructor_end_time');
        });
    }
}
