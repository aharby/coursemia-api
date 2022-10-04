<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssissmentRatingQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assissment_rating_questions', function (Blueprint $table) {
            $table->id();
            $table->longText('question')->nullable();
            $table->string('url')->nullable();
            $table->string("direction")->nullable();
            $table->unsignedBigInteger('rating_type')->nullable()->index();
            $table->foreign('rating_type','rating_type_fr')->references('id')->on('options')->onDelete('SET NULL');           
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
        Schema::dropIfExists('assissment_rating_questions');
    }
}
