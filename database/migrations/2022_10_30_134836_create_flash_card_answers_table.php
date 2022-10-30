<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlashCardAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flash_card_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_flashcard_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->boolean('answer');
            $table->timestamps();

            $table->foreign('course_flashcard_id')->references('id')
                ->on('course_flashcards')->onDelete('set null');
            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flash_card_answers');
    }
}
