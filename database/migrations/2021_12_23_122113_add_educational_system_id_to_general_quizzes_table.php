<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEducationalSystemIdToGeneralQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_quizzes', function (Blueprint $table) {
            $table->unsignedBigInteger("educational_system_id")->nullable();
            $table->foreign('educational_system_id')->references('id')->on('educational_systems')->onDelete('SET NULL');

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
            $table->dropForeign("general_quizzes_educational_system_id_foreign");
            $table->dropColumn("educational_system_id");
        });
    }
}
