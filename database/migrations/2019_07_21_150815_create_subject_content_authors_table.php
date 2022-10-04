<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectContentAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_content_authors', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('sme_id')->unsigned();
            $table->foreign('sme_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('content_author_id')->unsigned();
            $table->foreign('content_author_id')->references('id')->on('content_authors')->onDelete('cascade');

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
        Schema::dropIfExists('subject_content_authors');
    }
}
