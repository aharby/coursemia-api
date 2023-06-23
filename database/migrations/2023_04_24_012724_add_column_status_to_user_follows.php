<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnStatusToUserFollows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_follows', function (Blueprint $table) {
            $table->tinyInteger('status')
                ->default(0)
                ->after('followed_id')
                ->comment("0 => Pending, 1 => Approved");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_follows', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
