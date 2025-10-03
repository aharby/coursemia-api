<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::rename('course_users', 'student_courses');

        Schema::table('student_courses', function (Blueprint $table) {
            $table->renameColumn('user_id', 'student_id');
        });
    }

    public function down()
    {
        Schema::table('student_courses', function (Blueprint $table) {
            $table->renameColumn('student_id', 'user_id');
        });

        Schema::rename('student_courses', 'course_users');
    }

};
