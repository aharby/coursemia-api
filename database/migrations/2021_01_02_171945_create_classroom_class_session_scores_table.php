<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassroomClassSessionScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classroom_class_session_scores', function (Blueprint $table) {
            $table->id();
            $table->string('score_type')->nullable();

            $table->unsignedBigInteger('student_id')->nullable();
            $table->foreign('student_id')->references('id')
                ->on('users')->onDelete('SET NULL');

            $table->float('score')->nullable();
            
            $table->unsignedBigInteger('classroom_id')->nullable();
            $table->foreign('classroom_id')->references('id')
                ->on('classrooms')->onDelete('SET NULL');
            
            $table->unsignedBigInteger('classroom_session_id')->nullable();
            $table->foreign('classroom_session_id')->references('id')
                    ->on('classroom_class_sessions')->onDelete('SET NULL');
                    
            $table->softDeletes();
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
        Schema::dropIfExists('classroom_class_session_scores');
    }
}
