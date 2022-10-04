<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableAssessmentAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('assessment_user_id')->nullable()->index();
            $table->foreign('assessment_user_id')->references('id')
                ->on('assessment_users')->onDelete('SET NULL');

            $table->unsignedBigInteger('assessee_id')->nullable()->index();
            $table->foreign('assessee_id')->references('id')
                    ->on('users')->onDelete('SET NULL'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropForeign(['assessment_user_id']);
            $table->dropForeign(['assessee_id']);
            $table->dropColumn('assessment_user_id');
            $table->dropColumn('assessee_id');
        });
    }
}
