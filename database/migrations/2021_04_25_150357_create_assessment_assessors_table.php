<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentAssessorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_assessors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id')->nullable()->index();
            $table->foreign('assessment_id')->references('id')
                ->on('assessments')->onDelete('SET NULL');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')
                    ->on('users')->onDelete('SET NULL'); 
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
        Schema::dropIfExists('assessment_assessors');
    }
}
