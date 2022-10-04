<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResCompleteAcceptedAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_complete_accepted_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('answer')->nullable();

            $table->unsignedBigInteger('res_complete_question_id')->nullable()->index('complete_accepted_question_complete_ques_id_index');
            $table->foreign('res_complete_question_id', 'resource_accepted_answer_complete_question_id_fr')->references('id')->on('res_complete_questions')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_complete_accepted_answers');
    }
}
