<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsFromOffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('offers', 'title_en'))
        {
            Schema::table('offers', function (Blueprint $table)
            {
                $table->dropColumn('title_en');
            });
        }
        if (Schema::hasColumn('offers', 'title_ar'))
        {
            Schema::table('offers', function (Blueprint $table)
            {
                $table->dropColumn('title_ar');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offers', function (Blueprint $table) {
            //
        });
    }
}
