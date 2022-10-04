<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResMultipleChoiceOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_multiple_choice_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('answer')->nullable();
            $table->tinyInteger('is_correct_answer')->default(0);

            $table->unsignedBigInteger('res_multiple_choice_question_id')->nullable()->index('multi_choice_options_multi_choice_ques_id_index');
            $table->foreign('res_multiple_choice_question_id','resource_multiple_choice_question_id_fr')->references('id')->on('res_multiple_choice_questions')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_multiple_choice_options');
    }
}
