<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTitleArToNullableInCourseFlashcards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_flashcards', function (Blueprint $table) {
            $table->string('front_ar')->nullable()->change();
            $table->string('back_ar')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nullable_in_course_flashcards', function (Blueprint $table) {
            //
        });
    }
}
