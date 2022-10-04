<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToPreparationMediaTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('preparation_media','description')){
            Schema::table('preparation_media', function (Blueprint $table) {
                $table->text('description')->nullable();
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
            $table->dropColumn('description');
        });
    }
}
