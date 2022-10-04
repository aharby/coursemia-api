<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id')->nullable()->index();
            $table->foreign('assessment_id')->references('id')
                ->on('assessments')->onDelete('SET NULL');

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')
                    ->on('users')->onDelete('SET NULL'); 
            
            $table->unsignedBigInteger('assessee_id')->nullable()->index();
            $table->foreign('assessee_id')->references('id')
                    ->on('users')->onDelete('SET NULL'); 

            $table->boolean('is_finished')->default(false);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->float('score')->nullable();
            $table->string("general_comment")->nullable();

            $table->timestamps();
        });
        Schema::table('assessment_assessors', function (Blueprint $table) {
            $table->dropColumn(['is_finished','start_at','end_at','score','general_comment']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_users');
    }
}
