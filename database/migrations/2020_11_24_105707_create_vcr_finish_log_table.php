<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVcrFinishLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vcr_finish_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vcr_session_id')->nullable();
            $table->foreign('vcr_session_id')->on('vcr_sessions')->references('id')->onDelete('SET NULL');
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->foreign('closed_by')->on('users')->references('id')->onDelete('SET NULL');
            $table->string('closed_from')->nullable();
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
        Schema::dropIfExists('vcr_finish_log');
    }
}
