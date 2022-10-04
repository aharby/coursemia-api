<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_reports', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('general_exam_id')->nullable()->index();
            $table->foreign('general_exam_id')->references('id')->on('general_exams')->onDelete('SET NULL');

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
        Schema::dropIfExists('genral_exam_report');
    }
}
