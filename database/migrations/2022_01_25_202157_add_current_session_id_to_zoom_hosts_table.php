<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentSessionIdToZoomHostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zoom_hosts', function (Blueprint $table) {
            $table->unsignedBigInteger('current_vcr_session_id')->nullable();
            $table->foreign('current_vcr_session_id')->on('vcr_sessions')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zoom_hosts', function (Blueprint $table) {
            $table->dropColumn('current_vcr_session_id');
        });
    }
}
