<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseFlashcardsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_flashcards_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('front')->nullable();
            $table->string('back')->nullable();

            $table->string('locale')->index();

//            $table->unique(['course_flashcards_id', 'locale']);

            $table->bigInteger('course_flashcards_id')->unsigned();
            $table->foreign('course_flashcards_id')->references('id')->on('course_flashcards')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_flashcards_translations');
    }
}
