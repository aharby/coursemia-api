<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVcrSessionsPresence extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vcr_sessions_presence', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('vcr_session_id')->nullable()->index();
            $table->foreign('vcr_session_id')->references('id')
                ->on('vcr_sessions')->onDelete('SET NULL');

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('SET NULL');

            $table->string('vcr_session_type');
            $table->string('user_role');

            $table->timestamp('entered_at')->nullable();
            $table->timestamp('left_at')->nullable();

            $table->timestamp('session_time_to_start')->nullable();
            $table->timestamp('session_time_to_end')->nullable();

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
        Schema::dropIfExists('vcr_sessions_presence');
    }
}
