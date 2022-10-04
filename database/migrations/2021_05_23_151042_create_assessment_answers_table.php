<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
            $table->unsignedBigInteger('assessment_id')->nullable()->index();
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('SET NULL');
            $table->unsignedBigInteger('assessment_question_id')->nullable()->index();
            $table->foreign('assessment_question_id')->references('id')->on('assessment_questions')->onDelete('SET NULL');
            $table->float('score')->nullable();
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
        Schema::dropIfExists('assessment_answers');
    }
}
