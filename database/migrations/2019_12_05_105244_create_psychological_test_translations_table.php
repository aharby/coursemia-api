<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePsychologicalTestTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('psychological_test_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('instructions')->nullable();

            $table->string('locale')->index();

            $table->unique(['psychological_test_id','locale'], 'psychological_test_id_unique');

            $table->bigInteger('psychological_test_id')->unsigned();
            $table->foreign('psychological_test_id')->references('id')->on('psychological_tests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('psychological_test_translations');
    }
}
