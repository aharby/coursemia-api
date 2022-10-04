<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResCompleteQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_complete_questions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->text('question')->nullable();
            $table->text('question_feedback')->nullable();

            $table->unsignedBigInteger('res_complete_data_id')->nullable()->index();
            $table->decimal('time_to_solve',8,2)->nullable();

            $table->foreign('res_complete_data_id', 'res_complete_data_id_question_fr')->references('id')->on('res_complete_question_data')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_complete_questions');
    }
}
