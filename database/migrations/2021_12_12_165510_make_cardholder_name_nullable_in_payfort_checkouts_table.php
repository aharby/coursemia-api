<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeCardholderNameNullableInPayfortCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payfort_checkouts', function (Blueprint $table) {
            $table->string('card_holder_name',50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payfort_checkouts', function (Blueprint $table) {
            $table->string('card_holder_name',50)->nullable(false)->change();
        });
    }
}
