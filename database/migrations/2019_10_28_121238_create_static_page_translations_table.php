<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticPageTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('static_page_translations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title');
            $table->text('body')->nullable();

            $table->string('locale')->index();
            $table->unique(['static_page_id','locale']);

            $table->biginteger('static_page_id')->unsigned();
            $table->foreign('static_page_id')->references('id')->on('static_pages')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('static_pages_translations');
    }
}
