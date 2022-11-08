<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTitleArToNullableInCourseLectures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_lectures', function (Blueprint $table) {
            $table->string('title_ar')->nullable()->change();
            $table->text('description_ar')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nullable_in_course_lectures', function (Blueprint $table) {
            //
        });
    }
}
