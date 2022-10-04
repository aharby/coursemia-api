<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResMultipleChoiceDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_multiple_choice_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('description')->nullable();

            $table->unsignedBigInteger('resource_subject_format_subject_id')->nullable()->index('res_multi_choice_sub_for_sub_index_fr');
            $table->foreign('resource_subject_format_subject_id','resource_subject_format_subject_id_multiple_choice_fr')->references('id')->on('resource_subject_format_subject')->onDelete('SET NULL');

            $table->unsignedBigInteger('multiple_choice_type')->nullable()->index();
            $table->foreign('multiple_choice_type')->references('id')->on('options')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_multiple_choice_data');
    }
}
