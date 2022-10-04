<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllStudentQuiz extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('all_student_quiz', function (Blueprint $table) {
            $table->id();

            $table->string('quiz_type')->nullable();
            $table->string('status')->nullable();

            $table->float('quiz_result_percentage')->default(0);

            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')
                ->on('students')->onDelete('SET NULL');

            $table->unsignedBigInteger('quiz_id')->nullable()->index();
            $table->foreign('quiz_id')->references('id')
                ->on('quizzes')->onDelete('SET NULL');

            $table->timestamp('published_at')->nullable();
            $table->timestamp('taken_at')->nullable();
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
        Schema::dropIfExists('all_student_quiz');
    }
}
