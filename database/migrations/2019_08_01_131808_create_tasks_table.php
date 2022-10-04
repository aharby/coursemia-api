<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('is_expired')->default(0);
            $table->tinyInteger('is_assigned')->default(0);
            $table->tinyInteger('is_done')->default(0);
            $table->boolean('is_paused')->default(false);

            $table->integer('due_date')->nullable();

            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');


            $table->unsignedBigInteger('resource_subject_format_subject_id')->nullable()->index();
            $table->foreign('resource_subject_format_subject_id', 'resource_subject_format_subject_tasks_id')->references('id')->on('resource_subject_format_subject')->onDelete('SET NULL');

            $table->unsignedBigInteger('subject_format_subject_id')->nullable()->index();
            $table->foreign('subject_format_subject_id')->references('id')->on('subject_format_subject')->onDelete('SET NULL');

            $table->timestamp('pulled_at')->nullable();

            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')
                ->references('id')->on('users')->onDelete('SET NULL');

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
        Schema::dropIfExists('tasks');
    }
}
