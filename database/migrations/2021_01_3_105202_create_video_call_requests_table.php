<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoCallRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_call_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('to_user_id');
            $table->enum('status',['pending','accepted','rejected','cancelled'])->default('pending');
            $table->dateTime('supervisor_leave_time')->nullable();
            $table->string('channel');
            $table->dateTime('parent_leave_time')->nullable();
            $table->foreign('from_user_id')->on('users')->references('id');
            $table->foreign('student_id')->on('users')->references('id');
            $table->foreign('to_user_id')->on('users')->references('id');
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
        Schema::dropIfExists('video_call_requests');
    }
}
