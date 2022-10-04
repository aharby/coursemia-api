<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResCompleteDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_complete_question_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('description')->nullable();

            $table->unsignedBigInteger('resource_subject_format_subject_id')->nullable()->index('res_complete_sub_for_sub_index_fr');
            $table->foreign('resource_subject_format_subject_id', 'resource_subject_format_subject_id_complete_fr')->references('id')->on('resource_subject_format_subject')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_complete_question_data');
    }
}
