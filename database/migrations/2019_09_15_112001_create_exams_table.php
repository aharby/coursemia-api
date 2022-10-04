<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable();

            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->foreign('student_id')->references('id')->on('students')->onDelete('SET NULL');

            $table->unsignedBigInteger('creator_id')->nullable()->index();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('SET NULL');

            $table->integer('questions_number')->nullable();
            $table->string('difficulty_level', 40)->nullable();
            $table->string('type', 40)->nullable();

            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            $table->json('subject_format_subject_id')->nullable();

            $table->tinyInteger('is_finished')->default(0);
            $table->tinyInteger('is_started')->default(0);

            $table->dateTime('finished_time')->nullable();
            $table->dateTime('start_time')->nullable();

            $table->decimal('time_to_solve', 8, 2)->nullable();
            $table->decimal('student_time_to_solve', 8, 2)->nullable();
            $table->decimal('solving_speed_percentage', 8, 2)->nullable();

            $table->float('result')->nullable();


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
        Schema::dropIfExists('exams');
    }
}
