<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialityTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speciality_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();

            $table->string('locale')->index();

            $table->unique(['speciality_id','locale']);

            $table->bigInteger('speciality_id')->unsigned();
            $table->foreign('speciality_id')->references('id')->on('specialities')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('country_translations');
    }
}
