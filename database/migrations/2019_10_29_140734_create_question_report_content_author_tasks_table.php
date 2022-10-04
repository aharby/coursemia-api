<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionReportContentAuthorTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_report_content_author_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('task_id')->nullable()->index();
            $table->foreign('task_id', 'task_content_author_report_content_author_tasks')->references('id')->on('question_report_tasks')->onDelete('SET NULL');

            $table->unsignedBigInteger('content_author_id')->nullable()->index();
            $table->foreign('content_author_id' , 'content_author_report_content_author_tasks')->references('id')->on('content_authors')->onDelete('SET NULL');

            $table->unique(['task_id','content_author_id','deleted_at'] , 'question_report_content_author_tasks_unique_keys');

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
        Schema::dropIfExists('question_report_content_author_tasks');
    }
}
