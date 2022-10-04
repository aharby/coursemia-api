<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotSpotQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_hot_spot_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('question')->nullable();
            $table->decimal('image_width', 8,2)->nullable();
            $table->unsignedBigInteger('res_hot_spot_data_id')->nullable()->index();
            $table->longText('question_feedback')->nullable();
            $table->foreign('res_hot_spot_data_id', 'res_hot_spot_data_id_fr')->references('id')->on('res_hot_spot_data')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_hot_spot_questions');
    }
}
