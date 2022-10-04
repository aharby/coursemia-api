<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticBlockTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('static_block_translations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title');
            $table->text('body')->nullable();

            $table->string('locale')->index();
            $table->unique(['static_block_id','locale']);

            $table->bigInteger('static_block_id')->unsigned();
            $table->foreign('static_block_id')->references('id')->on('static_blocks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('static_blocks_translations');
    }
}
