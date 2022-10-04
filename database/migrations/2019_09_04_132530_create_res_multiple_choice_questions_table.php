<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResMultipleChoiceQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_multiple_choice_questions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->longText('question')->nullable();
            $table->string('url')->nullable();
            $table->longText('question_feedback')->nullable();
            $table->decimal('time_to_solve',8,2)->nullable();

            $table->unsignedBigInteger('res_multiple_choice_data_id')->nullable()->index();
            $table->foreign('res_multiple_choice_data_id', 'res_multiple_choice_data_id_question_fr')->references('id')->on('res_multiple_choice_data')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_multiple_choice_questions');
    }
}
