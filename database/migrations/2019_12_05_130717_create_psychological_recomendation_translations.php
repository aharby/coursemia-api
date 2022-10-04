<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsychologicalRecomendationTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psychological_recomendation_translations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->longText('result')->nullable();
            $table->longText('recomendation')->nullable();

            $table->string('locale')->index();

            $table->unique(['psychological_recomendation_id','locale'], 'psychological_recomendation_id_unique');

            $table->bigInteger('psychological_recomendation_id')->unsigned();
            $table->foreign('psychological_recomendation_id', 'psychological_recomendation_id_foreign')->references('id')->on('psychological_recomendations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('psychological_recomendation_translations');
    }
}
