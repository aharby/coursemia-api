<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSchoolSessionDataToVcrSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vcr_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('classroom_id')->nullable();
            $table->foreign('classroom_id')->references('id')->on('classrooms')
                ->onDelete('SET NULL');

            $table->unsignedBigInteger('classroom_session_id')->nullable();
            $table->foreign('classroom_session_id')->references('id')->on('classroom_class_sessions')
                ->onDelete('SET NULL');

            $table->string('subject_name')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vcr_sessions', function (Blueprint $table) {

            $table->dropForeign(['classroom_session_id']);
            $table->dropForeign(['classroom_id']);
            $table->dropColumn(['classroom_id' , 'classroom_session_id' , 'subject_name']);
        });
    }
}
