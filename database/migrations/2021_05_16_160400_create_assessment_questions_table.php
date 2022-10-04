<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->morphs('question');
            $table->unsignedBigInteger('assessment_id')->nullable();
            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->unsignedBigInteger('school_account_branch_id')->nullable();
            $table->foreign('school_account_branch_id')->references('id')->on('school_account_branches')->onDelete('SET NULL');
            $table->unsignedBigInteger('school_account_id')->nullable();
            $table->foreign('school_account_id')->references('id')->on('school_accounts')->onDelete('SET NULL');
            $table->string('slug')->nullable();
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
        Schema::dropIfExists('assessment_questions');
    }
}
