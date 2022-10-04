<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeetingProviderTypeToSchoolAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('school_accounts', function (Blueprint $table) {
            $table->string('meeting_type')->nullable()
                ->comment("this refers to which provider the school wants to serve its sessions");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_accounts', function (Blueprint $table) {
            $table->dropColumn('meeting_type');
        });
    }
}
