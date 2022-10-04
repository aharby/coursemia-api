<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDragDropQuestionAudioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drag_drop_question_audio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source_filename')->nullable();
            $table->string('filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('url')->nullable();
            $table->string('extension')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedBigInteger('res_drag_drop_question_id')->nullable()->index();
            $table->foreign('res_drag_drop_question_id','res_drag_drop_question_id_a_fr')
                ->references('id')
                ->on('res_drag_drop_questions')
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
        Schema::dropIfExists('drag_drop_question_audio');
    }
}
