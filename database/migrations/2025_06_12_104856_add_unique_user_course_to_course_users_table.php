<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::table('course_users', function (Blueprint $table) {
            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down()
    {
        Schema::table('course_users', function (Blueprint $table) {
            $table->dropUnique(['course_users_user_id_course_id_unique']);
        });
    }
};
