<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcademicalYearIdAndEducationalTermIdToBranchEducationalSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_educational_system', function (Blueprint $table) {
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->foreign('academic_year_id')->references('id')->on('options');
            $table->unsignedBigInteger('educational_term_id')->nullable();
            $table->foreign('educational_term_id')->references('id')->on('options');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_educational_system_grade_class', function (Blueprint $table) {
            //
        });
    }
}
