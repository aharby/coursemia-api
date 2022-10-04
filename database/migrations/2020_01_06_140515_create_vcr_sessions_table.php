<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\OurEdu\VCRSchedules\VCRSessionEnum;

class CreateVcrSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vcr_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');

            // for requested live sessions
            $table->unsignedBigInteger('student_id')->nullable()->index();  // can be moved to participants table
            $table->foreign('student_id')->references('id')->on('students')->onDelete('SET NULL');

            $table->unsignedBigInteger('instructor_id')->nullable()->index();
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('SET NULL');

            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            // for courses_types sessions
            $table->unsignedBigInteger('course_session_id')->nullable()->index();
            $table->foreign('course_session_id')->references('id')
                ->on('course_sessions')->onDelete('SET NULL');

            // for requested live sessions
            $table->unsignedBigInteger('vcr_request_id')->nullable()->index();
            $table->foreign('vcr_request_id')->references('id')->on('vcr_requests')->onDelete('SET NULL');

            // for courses_types sessions
            $table->unsignedBigInteger('course_id')->nullable()->index();
            $table->foreign('course_id')->references('id')
                ->on('courses')->onDelete('SET NULL');

            $table->double('price')->nullable();

            $table->string('status')->nullable();
            $table->string('vcr_session_type')
                ->default(VCRSessionEnum::REQUESTED_LIVE_SESSION)->index();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();


            $table->string('session_id')->nullable();
            $table->string('room_uuid')->nullable();

            // for requested live sessions
            $table->string('agora_student_uuid')->nullable()->index(); // can be moved to participants table
            $table->string('agora_instructor_uuid')->index();

            $table->timestamp('time_to_start')->nullable();
            $table->timestamp('time_to_end')->nullable();

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
        Schema::dropIfExists('vcr_sessions');
    }
}
