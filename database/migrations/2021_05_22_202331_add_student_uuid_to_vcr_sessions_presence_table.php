<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentUuidToVcrSessionsPresenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vcr_sessions_presence', function (Blueprint $table) {
            $table->string('user_uuid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vcr_sessions_presence', function (Blueprint $table) {
            $table->dropColumn('user_uuid');
        });
    }
}
