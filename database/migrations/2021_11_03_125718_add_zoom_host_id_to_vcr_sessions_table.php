<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZoomHostIdToVcrSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vcr_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('zoom_host_id')->nullable();
            $table->foreign('zoom_host_id')
                ->references('id')
                ->on('zoom_hosts');
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
            $table->dropForeign('vcr_sessions_zoom_host_id_foreign');
            $table->dropColumn('zoom_host_id');
        });
    }
}
