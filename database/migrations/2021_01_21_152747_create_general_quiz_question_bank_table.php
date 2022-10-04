<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralQuizQuestionBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_quiz_question_bank', function (Blueprint $table) {
            $table->id();
            $table->morphs('question');
            $table->unsignedBigInteger('general_quiz_id')->nullable();
            $table->foreign('general_quiz_id')->references('id')->on('general_quizzes')->onDelete('cascade');
            $table->unsignedBigInteger('school_account_branch_id')->nullable();
            $table->foreign('school_account_branch_id')->references('id')->on('school_account_branches')->onDelete('SET NULL');
            $table->unsignedBigInteger('school_account_id')->nullable();
            $table->foreign('school_account_id')->references('id')->on('school_accounts')->onDelete('SET NULL');
            $table->unsignedBigInteger('subject_format_subject_id');
            $table->foreign('subject_format_subject_id')->references('id')->on('subject_format_subject')->onDelete('cascade');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->on('subjects')->references('id')->onDelete('SET NULL');
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
        Schema::dropIfExists('general_quiz_question_bank');
    }
}
