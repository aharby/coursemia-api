<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGradeColorIdToGradeClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grade_classes', function (Blueprint $table) {
            $table->unsignedBigInteger("grade_color_id")->nullable();
            $table->foreign("grade_color_id")->references('id')->on('grade_colors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grade_classes', function (Blueprint $table) {
            $table->dropColumn("grade_color_id");
        });
    }
}
