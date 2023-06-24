<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFAQsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faqs_id');
            $table->string('question');
            $table->longText('answer');
            $table->string('locale')->index();
            $table->timestamps();

            $table->foreign('faqs_id')->references('id')->on('faqs')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('f_a_qs');
    }
}
