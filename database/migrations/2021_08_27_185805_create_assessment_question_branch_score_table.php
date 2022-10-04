<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentQuestionBranchScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_question_branch_score', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("assessment_question_id");
            $table->unsignedBigInteger("branch_id");
            $table->float("score");

            $table->foreign("assessment_question_id")
                ->on("assessment_questions")
                ->references("id")
                ->onDelete("cascade");

            $table->foreign("branch_id")
                ->on("school_account_branches")
                ->references("id")
                ->onDelete("cascade");

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
        Schema::dropIfExists('assessment_question_branch_score');
    }
}
