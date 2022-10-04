<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('quiz_type')->nullable();
            $table->string('quiz_time')->nullable();
            $table->string('creator_role')->nullable();

            $table->unsignedBigInteger('classroom_id')->nullable();
            $table->foreign('classroom_id')->references('id')
                ->on('classrooms')->onDelete('SET NULL');

            $table->unsignedBigInteger('classroom_class_id')->nullable();
            $table->foreign('classroom_class_id')->references('id')
                ->on('classroom_classes')->onDelete('SET NULL');

            $table->unsignedBigInteger('classroom_class_session_id')->nullable();
            $table->foreign('classroom_class_session_id')->references('id')
                ->on('classroom_class_sessions')->onDelete('SET NULL');

            $table->unsignedBigInteger('vcr_session_id')->nullable();
            $table->foreign('vcr_session_id')->references('id')
                ->on('vcr_sessions')->onDelete('SET NULL');

            $table->unsignedBigInteger('grade_class_id')->nullable();
            $table->foreign('grade_class_id')->references('id')
                ->on('grade_classes')->onDelete('SET NULL');

            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->foreign('created_by')->references('id')
                ->on('users')->onDelete('SET NULL');

            $table->timestamp('published_at')->nullable();
            $table->timestamp("start_at")->nullable();
            $table->timestamp("end_at")->nullable();
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
        Schema::dropIfExists('quizzes');
    }
}
