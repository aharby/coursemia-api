<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamReportContentAuthorTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_report_content_author_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('task_id')->nullable()->index('g_e_r_tasks');
            $table->foreign('task_id', 'g_e_r_tasks_ref')->references('id')->on('general_exam_report_tasks')->onDelete('SET NULL');

            $table->unsignedBigInteger('content_author_id')->nullable()->index('g_e_r_content_author');
            $table->foreign('content_author_id' , 'g_e_r_content_author_ref')->references('id')->on('content_authors')->onDelete('SET NULL');

            $table->unique(['task_id','content_author_id','deleted_at'] , 'g_e_r_content_author_tasks_unique_keys');

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
        Schema::dropIfExists('general_exam_report_content_author_tasks');
    }
}
