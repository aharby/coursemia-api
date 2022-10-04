<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('slug',20)->nullable();

            $table->unsignedBigInteger('exam_id')->nullable()->index();
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('SET NULL');

            $table->string('question_type',20);
            $table->morphs('question_table');

            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            $table->unsignedBigInteger('subject_format_subject_id')->nullable()->index();
            $table->foreign('subject_format_subject_id')->references('id')->on('subject_format_subject')->onDelete('SET NULL');

            $table->tinyInteger('is_correct_answer')->default(0);
            $table->tinyInteger('is_answered')->default(0);
            $table->decimal('time_to_solve',8,2)->nullable();
            $table->decimal('student_time_to_solve',8,2)->nullable();

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_questions');
    }
}
