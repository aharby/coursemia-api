<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResDragDropQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_drag_drop_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('question')->nullable();
            $table->string('image')->nullable();

            $table->unsignedBigInteger('res_drag_drop_data_id')->nullable()->index();
            $table->foreign('res_drag_drop_data_id', 'res_drag_drop_q_res_drag_drop_data_id_fr')->references('id')->on('res_drag_drop_data')->onDelete('SET NULL');

            $table->unsignedBigInteger('correct_option_id')->nullable()->index();
            $table->foreign('correct_option_id', 'res_drag_drop_question_res_drag_drop_options_fr')->references('id')->on('res_drag_drop_options')->onDelete('SET NULL');


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
        Schema::dropIfExists('res_drag_drop_questions');
    }
}
