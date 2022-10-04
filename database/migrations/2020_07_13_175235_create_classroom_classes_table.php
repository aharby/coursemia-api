<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassroomClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classroom_classes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreign('subject_id')->references('id')->on('subjects')
                ->onDelete('SET NULL');

            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->foreign('instructor_id')->references('id')->on('users')
                ->onDelete('SET NULL');

            $table->unsignedBigInteger('classroom_id')->nullable();
            $table->foreign('classroom_id')->references('id')->on('classrooms')
                ->onDelete('SET NULL');

            $table->date('from')->nullable()->index();
            $table->date('to')->nullable()->index();
            $table->time('from_time')->nullable()->index();
            $table->time('to_time')->nullable()->index();

//            $table->boolean('all_day')->default(0);

            $table->date('until_date')->nullable()->index();
            $table->boolean('repeat');
//            $table->boolean('repetition_times')->nullable();

            $table->boolean('sun')->default(false);
            $table->boolean('mon')->default(false);
            $table->boolean('tue')->default(false);
            $table->boolean('wed')->default(false);
            $table->boolean('thu')->default(false);
            $table->boolean('fri')->default(false);
            $table->boolean('sat')->default(false);

            $table->time('sun_from')->nullable();
            $table->time('mon_from')->nullable();
            $table->time('tue_from')->nullable();
            $table->time('wed_from')->nullable();
            $table->time('thu_from')->nullable();
            $table->time('fri_from')->nullable();
            $table->time('sat_from')->nullable();

            $table->time('sun_to')->nullable();
            $table->time('mon_to')->nullable();
            $table->time('tue_to')->nullable();
            $table->time('wed_to')->nullable();
            $table->time('thu_to')->nullable();
            $table->time('fri_to')->nullable();
            $table->time('sat_to')->nullable();

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
        Schema::dropIfExists('classroom_subject_sessions');
    }
}
