<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamStudentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_student_answers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('SET NULL');

            $table->unsignedBigInteger('general_exam_id')->nullable()->index();
            $table->foreign('general_exam_id')->references('id')->on('general_exams')->onDelete('SET NULL');

            $table->unsignedBigInteger('general_exam_question_id')->nullable()->index();
            $table->foreign('general_exam_question_id')->references('id')->on('general_exam_questions')->onDelete('SET NULL');

            $table->unsignedBigInteger('general_exam_option_id')->nullable()->index();
            $table->foreign('general_exam_option_id')->references('id')->on('general_exam_options')->onDelete('SET NULL');

            $table->string('answer_text')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->string('time_to_solve')->nullable();

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
        Schema::dropIfExists('general_exam_student_answer');
    }
}
