<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizQuestionsOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_questions_options', function (Blueprint $table) {
            $table->id();
            $table->longText('option')->nullable();
            $table->boolean('is_correct_answer')->nullable()->default(false);

            $table->unsignedBigInteger('quiz_question_id')->nullable();
            $table->foreign('quiz_question_id')->references('id')
                ->on('quiz_questions')->onDelete('SET NULL');
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
        Schema::dropIfExists('quiz_questions_options');
    }
}
