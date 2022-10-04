<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamReportTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_report_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('is_expired')->default(0);
            $table->tinyInteger('is_assigned')->default(0);
            $table->tinyInteger('is_done')->default(0);
            $table->boolean('is_paused')->default(false);
            $table->timestamp('pulled_at')->nullable();

            $table->string('due_date', 5)->nullable();

            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

            $table->unsignedBigInteger('general_exam_report_question_id')->nullable()->index('g_e_report_question_id');
            $table->foreign('general_exam_report_question_id', 'g_e_report_question_ref_id')->references('id')->on('general_exam_report_questions')->onDelete('SET NULL');

            $table->morphs('question');
            $table->string('slug');

            $table->unsignedBigInteger('subject_format_subject_id')->nullable()->index();
            $table->foreign('subject_format_subject_id')->references('id')->on('subject_format_subject')->onDelete('SET NULL');

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
        Schema::dropIfExists('general_exam_report_tasks');
    }
}
