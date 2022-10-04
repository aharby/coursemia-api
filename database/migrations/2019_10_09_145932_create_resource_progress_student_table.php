<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourceProgressStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_progress_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('SET NULL');

            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            $table->unsignedBigInteger('subject_format_id')->nullable()->index();
            $table->foreign('subject_format_id')->references('id')->on('subject_format_subject')->onDelete('SET NULL');


            $table->string('resource_slug');

            $table->unsignedBigInteger('resource_id')->nullable()->index();
            $table->foreign('resource_id')->references('id')->on('resource_subject_format_subject')->onDelete('SET NULL');

            $table->decimal('points',8,2)->default(0);

            $table->boolean('is_visible')->default(false);
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
        Schema::dropIfExists('resource_progress_student');
    }
}
