<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResMultiMatchingQuestionOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_multi_matching_question_option', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('res_multi_matching_option_id')->nullable()->index('res_multi_matching_option_index_fr');
            $table->foreign('res_multi_matching_option_id', 'res_multi_matching_option_rel_fr')->references('id')->on('res_multi_matching_options')->onDelete('SET NULL');

            $table->unsignedBigInteger('res_multi_matching_question_id')->nullable()->index('res_multi_matching_question_index_fr');
            $table->foreign('res_multi_matching_question_id', 'res_multi_matching_question_rel_fr')->references('id')->on('res_multi_matching_questions')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_multi_matching_question_option');
    }
}
