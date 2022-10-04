<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionPreparationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_preparations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            $table->unsignedBigInteger('classroom_id')->nullable();
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('SET NULL');

            $table->unsignedBigInteger('classroom_class_id')->nullable();
            $table->foreign('classroom_class_id')->references('id')->on('classroom_classes')->onDelete('SET NULL');

            $table->unsignedBigInteger('classroom_session_id')->nullable();
            $table->foreign('classroom_session_id')->references('id')->on('classroom_class_sessions')->onDelete('SET NULL');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('SET NULL');

            $table->longText('internal_preparation')->nullable();
            $table->longText('student_preparation')->nullable();
            $table->timestamp('published_at')->nullable();
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
        Schema::dropIfExists('session_preparations');
    }
}
