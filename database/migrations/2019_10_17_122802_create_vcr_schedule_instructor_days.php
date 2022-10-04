<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVcrScheduleInstructorDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vcr_schedule_instructor_days', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('day')->nullable();

            $table->unsignedBigInteger('vcr_schedule_instructor_id')->nullable()->index();
            $table->foreign('vcr_schedule_instructor_id')->references('id')->on('vcr_schedule_instructor')->onDelete('cascade');

            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();

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
        Schema::dropIfExists('vcr_schedule_instructor_days');
    }
}
