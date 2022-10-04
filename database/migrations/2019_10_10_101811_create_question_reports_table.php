<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');
            $table->unsignedBigInteger('subject_format_subject_id')->nullable();
            $table->foreign('subject_format_subject_id')->references('id')->on('subject_format_subject')->onDelete('SET NULL');
            $table->string('difficulty_level')->nullable();
            $table->string('difficulty_level_result_equation')->nullable();
            $table->morphs('question');
            $table->text('header')->nullable();
            $table->integer('total_answer')->nullable();
            $table->integer('correct_answer')->nullable();
            $table->tinyInteger('is_ignored')->default(0);
            $table->tinyInteger('is_reported')->default(0);
            $table->dateTime('last_update')->nullable();
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
        Schema::dropIfExists('question_reports');
    }
}
