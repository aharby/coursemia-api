<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsRepeatedToGeneralQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_quizzes', function (Blueprint $table) {
            $table->tinyInteger("is_repeated")->default(false)->after("school_account_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_quizzes', function (Blueprint $table) {
            $table->dropColumn("is_repeated");
        });
    }
}
