<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        
        Schema::table('order_course', function (Blueprint $table) {

            $table->dropForeign(['course_id']);
            $table->dropForeign(['order_id']);

        });

        Schema::rename('order_course', 'student_order_courses');

        Schema::table('student_order_courses', function (Blueprint $table) {

            $table->renameColumn('order_id', 'student_order_id');

            // Add the new foreign key
            $table->foreign('student_order_id')
                  ->references('id')
                  ->on('student_orders')
                  ->onDelete('cascade');

            $table->foreign('course_id')
                  ->references('id')
                  ->on('courses')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('student_order_courses', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropForeign(['student_order_id']);
            $table->renameColumn('student_order_id', 'order_id');
        });

        Schema::rename('student_order_courses', 'order_course');

        Schema::table('order_course', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }
};
