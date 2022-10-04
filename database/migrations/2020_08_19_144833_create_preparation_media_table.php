<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreparationMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preparation_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('preparation_id')->nullable();
            $table->foreign('preparation_id')->references('id')->on('session_preparations')->onDelete('SET NULL');
            $table->unsignedBigInteger('subject_id')->nullable()->index();
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('SET NULL');
            $table->boolean('library')->default(0);
            $table->boolean('school_public')->default(0);
            $table->string('source_filename')->nullable();
            $table->string('filename')->nullable();
            $table->integer('size')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('url')->nullable();
            $table->string('extension')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preparation_media');
    }
}
