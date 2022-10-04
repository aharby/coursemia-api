<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResTrueFalseDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_true_false_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('description')->nullable();

            $table->unsignedBigInteger('resource_subject_format_subject_id')->nullable()->index();
            $table->foreign('resource_subject_format_subject_id', 'resource_subject_format_subject_id_true_false_fr')->references('id')->on('resource_subject_format_subject')->onDelete('SET NULL');

            $table->unsignedBigInteger('true_false_type')->nullable()->index();
            $table->foreign('true_false_type')->references('id')->on('options')->onDelete('SET NULL');

            $table->unsignedBigInteger('question_type')->nullable()->index();
            $table->foreign('question_type')->references('id')->on('options')->onDelete('SET NULL');


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
        Schema::dropIfExists('res_true_false_data');
    }
}
