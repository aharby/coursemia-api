<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectSchoolInstructorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_school_instructor', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')
                ->onDelete('SET NULL');
            $table->foreign('instructor_id')->references('id')->on('users')
                ->onDelete('SET NULL');
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
        Schema::dropIfExists('subject_school_instructor');
    }
}
