<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->on('subjects')->references('id')->onDelete('SET NULL');
            $table->json('subject_sections')->nullable();
            $table->string('quiz_type')->nullable();
            $table->string('title')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->boolean('random_question')->default(false);
            $table->dateTime('end_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('school_account_branches')->onDelete('SET NULL');
            $table->unsignedBigInteger('school_account_id')->nullable();
            $table->foreign('school_account_id')->references('id')->on('school_accounts')->onDelete('SET NULL');
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->foreign('created_by')->references('id')
                ->on('users')->onDelete('SET NULL');
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
        Schema::dropIfExists('general_quizzes');
    }
}
