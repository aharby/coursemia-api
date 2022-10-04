<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResMatchingQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_matching_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('text')->nullable();

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
        Schema::dropIfExists('res_matching_questions');
    }
}
