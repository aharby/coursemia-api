<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotSpotAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_hot_spot_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('answer')->nullable();

            $table->unsignedBigInteger('res_hot_spot_question_id')->nullable()->index('res_hot_spot_question_id_index');
            $table->foreign('res_hot_spot_question_id', 'res_hot_spot_question_id_fr')->references('id')->on('res_hot_spot_questions')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_hot_spot_answers');
    }
}
