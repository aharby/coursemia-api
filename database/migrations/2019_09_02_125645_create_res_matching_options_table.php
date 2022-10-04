<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResMatchingOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_matching_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('option')->nullable();

            $table->unsignedBigInteger('res_matching_question_id')->nullable()->index();
            $table->foreign('res_matching_question_id')->references('id')->on('res_matching_questions')->onDelete('SET NULL');

            $table->unsignedBigInteger('res_matching_data_id')->nullable()->index();
            $table->foreign('res_matching_data_id')->references('id')->on('res_matching_data')->onDelete('SET NULL');


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
        Schema::dropIfExists('res_matching_options');
    }
}
