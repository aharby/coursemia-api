<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultipleChoiceQuestionMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multiple_choice_question_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source_filename')->nullable();
            $table->string('filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('url')->nullable();
            $table->string('extension')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('res_multiple_choice_question_id')->nullable()->index('res_m_choice_q');
            $table->foreign('res_multiple_choice_question_id','res_m_choice_q')
                ->references('id')
                ->on('res_multiple_choice_questions')
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
        Schema::dropIfExists('multiple_choice_question_media');
    }
}
