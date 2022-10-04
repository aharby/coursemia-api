<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsychologicalOptionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psychological_option_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();

            $table->string('locale')->index();

            $table->unique(['psychological_option_id','locale'], 'psychological_option_id_unique');

            $table->bigInteger('psychological_option_id')->unsigned();
            $table->foreign('psychological_option_id', 'psychological_option_id_foreign')->references('id')->on('psychological_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('psychological_option_translations');
    }
}
