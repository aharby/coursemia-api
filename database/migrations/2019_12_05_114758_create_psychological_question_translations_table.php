<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsychologicalQuestionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psychological_question_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();

            $table->string('locale')->index();

            $table->unique(['psychological_question_id','locale'], 'psychological_question_id_unique');

            $table->bigInteger('psychological_question_id')->unsigned();
            $table->foreign('psychological_question_id', 'psychological_question_id_foreign')->references('id')->on('psychological_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('psychological_question_translations');
    }
}
