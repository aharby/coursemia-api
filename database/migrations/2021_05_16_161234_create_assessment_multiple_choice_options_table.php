<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentMultipleChoiceOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_multiple_choice_options', function (Blueprint $table) {
            $table->id();
            $table->longText('answer')->nullable();
            $table->unsignedBigInteger('assessment_mcq_id')->nullable()->index('multi_choice_option_assessment_mcq_id_index');
            $table->foreign('assessment_mcq_id','assessment_mcq_id_fr')->references('id')->on('assessment_multiple_choice_questions')->onDelete('SET NULL');
            $table->float("grade")->default(0);
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
        Schema::dropIfExists('assessment_multiple_choice_options');
    }
}
