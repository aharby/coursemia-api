<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResTrueFalseOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_true_false_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('option')->nullable();
            $table->tinyInteger('is_correct_answer')->default(0);

            $table->unsignedBigInteger('res_true_false_question_id')->nullable()->index();
            $table->foreign('res_true_false_question_id')->references('id')->on('res_true_false_questions')->onDelete('SET NULL');


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
        Schema::dropIfExists('res_true_false_options');
    }
}
