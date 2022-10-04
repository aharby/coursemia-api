<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAssessmentQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assessment_multiple_choice_questions', function (Blueprint $table) {
            $table->dropForeign('multiple_choice_type_fr');
            $table->dropColumn('multiple_choice_type');
            $table->string('slug')->nullable();
        });

        Schema::table('assissment_rating_questions', function (Blueprint $table) {
            $table->dropForeign('rating_type_fr');
            $table->dropColumn('rating_type');
            $table->string('slug')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assessment_multiple_choice_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('multiple_choice_type')->nullable()->index();
            $table->foreign('multiple_choice_type','multiple_choice_type_fr')->references('id')->on('options')->onDelete('SET NULL');     
            $table->dropColumn('slug');
        });

        Schema::table('assissment_rating_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('rating_type')->nullable()->index();
            $table->foreign('rating_type','rating_type_fr')->references('id')->on('options')->onDelete('SET NULL');     
            $table->dropColumn('slug');
        });


    }
}
