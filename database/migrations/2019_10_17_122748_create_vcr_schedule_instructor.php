<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVcrScheduleInstructor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vcr_schedule_instructor', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('instructor_id')->nullable()->index();
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('SET NULL');

            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            $table->double('price')->nullable();

            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->boolean('is_active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vcr_schedule_instructor');
    }
}
