<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessmentMultipleChoiceQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_multiple_choice_questions', function (Blueprint $table) {
            $table->id();
            $table->longText('question')->nullable();
            $table->string('url')->nullable();
            $table->unsignedBigInteger('multiple_choice_type')->nullable()->index();
            $table->foreign('multiple_choice_type','multiple_choice_type_fr')->references('id')->on('options')->onDelete('SET NULL');           
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
        Schema::dropIfExists('assessment_multiple_choice_questions');
    }
}
