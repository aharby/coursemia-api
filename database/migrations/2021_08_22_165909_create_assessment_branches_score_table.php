<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentBranchesScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_branches_score', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("assessment_id");
            $table->unsignedBigInteger("branch_id");
            $table->float("score")->default(0.0);

            $table->foreign("assessment_id")
                ->on("assessments")
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
        Schema::dropIfExists('assessment_branches_score');
    }
}
