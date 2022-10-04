<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * The related subjects to a student and teacher relation pivot table
 */
class CreateTeacherStudentSubjectsRelationalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_student_subject', function (Blueprint $table) {
            $table->unsignedBigInteger('student_student_teacher_id')->index();
            $table->unsignedBigInteger('subject_id')->index();

            $table->foreign('student_student_teacher_id')
                ->references('id')
                ->on('student_student_teacher')
                ->onDelete('cascade');

            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');

            $table->unique(['student_student_teacher_id', 'subject_id'], 'relation_subjects_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_student_subject');
    }
}
