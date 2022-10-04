<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->string('subscription_cost')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->tinyInteger('out_of_date')->default(0);
            $table->string('picture')->nullable();
            $table->tinyInteger('is_active')->default(0);
            $table->unsignedBigInteger('subject_id')
                ->nullable()
                ->index();
            $table->unsignedBigInteger('instructor_id')
                ->nullable()
                ->index();

            $table->unsignedBigInteger('created_by')
                ->nullable()
                ->index();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('SET NULL');

            $table->foreign('instructor_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
