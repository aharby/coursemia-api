<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssissmentRatingOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assissment_rating_options', function (Blueprint $table) {
            $table->id();
            $table->longText('answer')->nullable();
            $table->tinyInteger('order');
            $table->string('satisfication_slug')->nullable();
            $table->unsignedBigInteger('assessment_rating_question_id')->nullable()->index('rating_option_assessment_ratingquestion_id_index');
            $table->foreign('assessment_rating_question_id','assessment_rating_id_fr')->references('id')->on('assissment_rating_questions')->onDelete('SET NULL');
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
        Schema::dropIfExists('assissment_rating_options');
    }
}
