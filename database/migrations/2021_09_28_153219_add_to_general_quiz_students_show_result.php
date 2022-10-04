<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToGeneralQuizStudentsShowResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_quiz_students', function (Blueprint $table) {
            $table->boolean('show_result')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_quiz_students', function (Blueprint $table) {
            $table->dropColumn('show_result');
        });
    }
}
