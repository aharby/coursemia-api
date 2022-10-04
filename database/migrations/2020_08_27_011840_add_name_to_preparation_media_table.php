<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToPreparationMediaTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('preparation_media','name')){
            Schema::table('preparation_media', function (Blueprint $table) {
                $table->string('name')->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::table('preparation_media', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
