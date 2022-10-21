<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseNoteTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_note_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_note_id')->index();
            $table->string('title')->nullable();
            $table->string('locale');

            $table->foreign('course_note_id')->references('id')->on('course_notes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_note_translations');
    }
}
