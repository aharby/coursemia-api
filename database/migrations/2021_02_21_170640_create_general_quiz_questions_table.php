<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_quiz_question', function (Blueprint $table) {
            $table->unsignedBigInteger("question_id");
            $table->unsignedBigInteger("general_quiz_id");
            $table->foreign("question_id")->references("id")->on("general_quiz_question_bank")->onDelete("cascade");
            $table->foreign("general_quiz_id")->references("id")->on("general_quizzes")->onDelete("cascade");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_quiz_question');
    }
}
