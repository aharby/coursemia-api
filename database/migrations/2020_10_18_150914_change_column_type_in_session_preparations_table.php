<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypeInSessionPreparationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session_preparations', function (Blueprint $table) {
            $table->longText("internal_preparation")->change();
            $table->longText("student_preparation")->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('session_preparations', function (Blueprint $table) {
            $table->text("internal_preparation")->change();
            $table->text("student_preparation")->change();
        });
    }
}
