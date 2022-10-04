<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVcrReminder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vcr_reminder', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');

            $table->string('user_email',255)->nullable();
            $table->string('user_role',100)->nullable();


            $table->unsignedBigInteger('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('vcr_sessions')->onDelete('SET NULL');

            $table->string('session_type',100)->nullable();

            $table->string('room_uuid',255)->nullable();
            $table->string('user_uuid',255)->nullable();


            $table->dateTime('session_start_date_time')->nullable();
            $table->dateTime('session_end_date_time')->nullable();


            $table->tinyInteger('sent_first')->default(0);
            $table->tinyInteger('sent')->default(0);

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
        Schema::dropIfExists('vcr_reminder');
    }
}
