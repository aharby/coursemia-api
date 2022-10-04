<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcademicYearSchoolAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic_year_school_account', function (Blueprint $table) {
            $table->unsignedBigInteger('school_account_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->foreign('school_account_id')->references('id')->on('school_accounts')
                ->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('options')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic_year_school_account');
    }
}
