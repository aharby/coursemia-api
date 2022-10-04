<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResTrueFalseQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_true_false_questions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('text')->nullable();
            $table->longText('question_feedback')->nullable();
            $table->string('image')->nullable();

            $table->tinyInteger('is_true')->default(0);
            $table->decimal('time_to_solve',8,2)->nullable();

            $table->unsignedBigInteger('res_true_false_data_id')->nullable()->index();
            $table->foreign('res_true_false_data_id', 'res_true_false_data_id_question_fr')->references('id')->on('res_true_false_data')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_true_false_questions');
    }
}
