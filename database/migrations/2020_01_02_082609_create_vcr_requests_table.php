<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVcrRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vcr_requests', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('SET NULL');

            $table->unsignedBigInteger('instructor_id')->nullable()->index();
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('SET NULL');

            $table->unsignedBigInteger('vcr_schedule_id')->nullable()->index();
            $table->foreign('vcr_schedule_id')->references('id')->on('vcr_schedule_instructor')->onDelete('SET NULL');


            $table->unsignedBigInteger('vcr_day_id')->nullable()->index();
            $table->foreign('vcr_day_id')->references('id')->on('vcr_schedule_instructor_days')->onDelete('SET NULL');

            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            $table->unsignedBigInteger('exam_id')->nullable()->index();
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('SET NULL');

            $table->timestamp('accepted_at')->nullable();

            $table->double('price')->nullable();

            $table->string('status')->nullable();

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
        Schema::dropIfExists('live_session_requests');
    }
}
