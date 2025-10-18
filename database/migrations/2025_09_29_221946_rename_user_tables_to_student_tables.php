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
        Schema::rename('user_devices', 'student_devices');
        Schema::table('student_devices', function (Blueprint $table) {
            $table->renameColumn('user_id', 'student_id');
        });

        // user_question_answers -> student_question_answers
        Schema::rename('user_question_answers', 'student_question_answers');
        Schema::table('student_question_answers', function (Blueprint $table) {
            $table->renameColumn('user_id', 'student_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_tables', function (Blueprint $table) {
            //
        });
    }
};
