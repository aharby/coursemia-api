<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEducationalSystemTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('educational_system_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();

            $table->string('locale')->index();

            $table->unique(['educational_system_id','locale'] ,'educational_system_id_trans_with_locale');

            $table->bigInteger('educational_system_id')->unsigned();
            $table->foreign('educational_system_id')->references('id')->on('educational_systems')->onDelete('cascade');

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
        Schema::dropIfExists('educational_system_translations');
    }
}
