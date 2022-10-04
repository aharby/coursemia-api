<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('birth_date')->nullable();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');

            $table->unsignedBigInteger('educational_system_id')->nullable()->index();
            $table->foreign('educational_system_id')->references('id')->on('educational_systems')->onDelete('SET NULL');

            $table->unsignedBigInteger('school_id')->nullable()->index();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('SET NULL');

            $table->unsignedBigInteger('class_id')->nullable()->index();
            $table->foreign('class_id')->references('id')->on('grade_classes')->onDelete('SET NULL');

            $table->unsignedBigInteger('academical_year_id')->nullable()->index();
            $table->foreign('academical_year_id')->references('id')->on('options')->onDelete('SET NULL');

            $table->float('wallet_amount',8,2)->nullable()->default(0.00);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
