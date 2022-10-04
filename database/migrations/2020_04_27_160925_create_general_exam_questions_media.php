<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamQuestionsMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_questions_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('source_filename')->nullable();
            $table->string('filename')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('url')->nullable();
            $table->string('extension')->nullable();
            $table->boolean('status')->default(1);

            $table->unsignedBigInteger('general_exam_question_id')->nullable()->index();
            $table->foreign('general_exam_question_id')->references('id')->on('general_exam_questions')->onDelete('CASCADE');

            $table->unsignedBigInteger('general_exam_q_child_id')->nullable()->index();
            $table->foreign('general_exam_q_child_id')->references('id')->on('general_exam_question_questions')->onDelete('CASCADE');

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
        Schema::dropIfExists('general_exam_questions_media');
    }
}
