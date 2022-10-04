<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportSubjectFormatSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_subject_format_subject', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('section_id')->nullable()->index('report_child_section_id_fr');
            $table->foreign('section_id', 'report_child_section_id_ref_fr')->references('id')->on('subject_format_subject')->onDelete('SET NULL');

            $table->unsignedBigInteger('section_parent_id')->nullable()->index('report_parent_section_id_fr');
            $table->foreign('section_parent_id', 'report_parent_section_id_ref_fr')->references('id')->on('subject_format_subject')->onDelete('SET NULL');

            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');

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
        Schema::dropIfExists('report_subject_format_subject');
    }
}
