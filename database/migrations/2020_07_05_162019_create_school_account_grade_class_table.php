<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolAccountGradeClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('school_account_grade_class', function (Blueprint $table) {
            $table->unsignedBigInteger('school_account_id')->nullable();
            $table->unsignedBigInteger('grade_class_id')->nullable();
            $table->foreign('school_account_id')->references('id')->on('school_accounts')
                ->onDelete('cascade');
            $table->foreign('grade_class_id')->references('id')->on('grade_classes')
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
        Schema::dropIfExists('school_account_grade_class');
    }
}
