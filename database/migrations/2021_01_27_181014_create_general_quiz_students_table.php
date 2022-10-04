<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralQuizStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_quiz_students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');
            $table->unsignedBigInteger('general_quiz_id')->nullable()->index();
            $table->foreign('general_quiz_id')->references('id')->on('general_quizzes')->onDelete('SET NULL');
            $table->boolean('is_finished')->default(false);
            $table->dateTime('finished_time')->nullable();
            $table->float('result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_quiz_students');
    }
}
