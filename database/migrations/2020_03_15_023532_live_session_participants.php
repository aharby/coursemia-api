<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LiveSessionParticipants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('live_session_participants', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')
                ->on('students')->onDelete('SET NULL');

            $table->unsignedBigInteger('session_id')->nullable()->index();
            $table->foreign('session_id')->references('id')
                ->on('course_sessions')->onDelete('SET NULL');

            $table->unsignedBigInteger('course_id')->nullable()->index();
            $table->foreign('course_id')->references('id')
                ->on('courses')->onDelete('SET NULL');

            $table->string('course_type')->nullable()->index();

            $table->string('agora_student_uuid')->index();
            $table->string('agora_instructor_uuid')->index();
            $table->string('room_uuid')->index();
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
        Schema::dropIfExists('live_session_participants');
    }
}
