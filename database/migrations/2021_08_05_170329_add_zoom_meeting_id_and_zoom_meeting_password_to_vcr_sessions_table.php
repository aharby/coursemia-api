<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZoomMeetingIdAndZoomMeetingPasswordToVcrSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vcr_sessions', function (Blueprint $table) {
            $table->bigInteger("zoom_meeting_id")->nullable();
            $table->string("zoom_meeting_password")->nullable();
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
            $table->dropColumn("zoom_meeting_id");
            $table->dropColumn("zoom_meeting_password");
        });
    }
}
