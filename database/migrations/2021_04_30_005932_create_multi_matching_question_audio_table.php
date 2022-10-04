<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultiMatchingQuestionAudioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multi_matching_question_audio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source_filename')->nullable();
            $table->string('filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('url')->nullable();
            $table->string('extension')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('res_multi_matching_question_id')->nullable()->index('res_m_match_a');
            $table->foreign('res_multi_matching_question_id','res_m_match_a')
                ->references('id')
                ->on('res_multi_matching_questions')
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
        Schema::dropIfExists('multi_matching_question_audio');
    }
}
