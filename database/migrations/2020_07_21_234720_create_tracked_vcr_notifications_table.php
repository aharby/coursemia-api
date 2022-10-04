<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackedVcrNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracked_vcr_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('SET NULL');

            $table->string('vcr_session_type')->nullable();
            $table->string('user_role')->nullable();

            $table->unsignedBigInteger('vcr_session_id')->nullable()->index();
            $table->foreign('vcr_session_id')->references('id')
                ->on('vcr_sessions')->onDelete('SET NULL');

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
        Schema::dropIfExists('tracked_vcr_notifications');
    }
}
