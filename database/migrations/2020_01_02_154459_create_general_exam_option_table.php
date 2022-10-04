<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralExamOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_exam_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('option');
            $table->unsignedBigInteger('general_exam_question_id')

                ->index();

            $table->unsignedBigInteger('general_exam_question_question_id')
                ->nullable()
                ->index('g_e_q_question_id_fr');
            $table->boolean('is_correct')->nullable()->default(false);
            $table->boolean('is_main_answer')->nullable()->default(false);

            $table->timestamps();

            $table->foreign('general_exam_question_id')->references('id')->on('general_exam_questions')->onDelete('CASCADE');
            $table->foreign('general_exam_question_question_id' , 'g_e_q_question_id_ref_fr')->references('id')->on('general_exam_question_questions')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('general_exam_options');
    }
}
