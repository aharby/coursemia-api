<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAudioVideoMultipleChoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('res_multiple_choice_questions', function (Blueprint $table) {
            $table->text('audio_link')->nullable();
            $table->text('video_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('res_multiple_choice_questions', function (Blueprint $table) {
            $table->dropColumn('audio_link');
            $table->dropColumn('video_link');
        });
    }
}
