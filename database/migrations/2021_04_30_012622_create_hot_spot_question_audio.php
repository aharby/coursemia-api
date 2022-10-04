<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotSpotQuestionAudio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hot_spot_question_audio', function (Blueprint $table) {
            $table->id();
            $table->string('source_filename')->nullable();
            $table->string('filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('url')->nullable();
            $table->string('extension')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('res_hot_spot_question_id')->nullable()->index('res_m_choice_q');
            $table->foreign('res_hot_spot_question_id','res_hot_spot_a')
                ->references('id')
                ->on('res_hot_spot_questions')
                ->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('hot_spot_question_audio');
    }
}
