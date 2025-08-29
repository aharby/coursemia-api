<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename table
        Schema::rename('order_course', 'student_order_courses');

        // Rename foreign key column inside the table
        Schema::table('student_order_courses', function (Blueprint $table) {
            // Drop old foreign key first
            $table->dropForeign(['order_id']);

            // Rename the column
            $table->renameColumn('order_id', 'student_order_id');

            // Add the new foreign key
            $table->foreign('student_order_id')
                  ->references('id')
                  ->on('student_orders')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_order_courses', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['student_order_id']);

            // Rename back the column
            $table->renameColumn('student_order_id', 'order_id');

            // Restore old foreign key
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
        });

        // Rename the table back
        Schema::rename('student_order_courses', 'order_course');
    }
};
