<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAdminIdToQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable()->after('id');

            $table->foreign('admin_id')->references('id')
                ->on('admins')->onDelete('set null');
        });

        Schema::table('course_lectures', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable()->after('id');

            $table->foreign('admin_id')->references('id')
                ->on('admins')->onDelete('set null');
        });

        Schema::table('course_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable()->after('id');

            $table->foreign('admin_id')->references('id')
                ->on('admins')->onDelete('set null');
        });

        Schema::table('course_flashcards', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable()->after('id');

            $table->foreign('admin_id')->references('id')
                ->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            //
        });
    }
}
