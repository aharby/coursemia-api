<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsFromCourseFlashCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $flashs = \App\Modules\Courses\Models\CourseFlashcard::get();
        foreach ($flashs as $flash){
            $flashTrans = new \App\Modules\Courses\Models\CourseFlashcardTranslation;
            $flashTrans->front = $flash->front_en;
            $flashTrans->back = $flash->back_en;
            $flashTrans->locale = 'en';
            $flashTrans->course_flashcards_id = $flash->id;
            $flashTrans->save();
            $flashTrans = new \App\Modules\Courses\Models\CourseFlashcardTranslation;
            $flashTrans->front = $flash->front_ar;
            $flashTrans->back = $flash->back_ar;
            $flashTrans->locale = 'ar';
            $flashTrans->course_flashcards_id = $flash->id;
            $flashTrans->save();
        }
        Schema::table('course_flashcards', function (Blueprint $table) {
            $table->dropColumn('front_en', 'front_ar', 'back_en', 'back_ar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_flash_cards', function (Blueprint $table) {
            //
        });
    }
}
