<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVcrSessionMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('vcr_session_media')){
            Schema::create('vcr_session_media', function (Blueprint $table) {
                $table->id();
                $table->string('source_filename')->nullable();
                $table->string('filename')->nullable();
                $table->string('mime_type')->nullable();
                $table->string('url')->nullable();
                $table->string('extension')->nullable();
                $table->boolean('status')->default(1);

                $table->unsignedBigInteger('vcr_session_id')->nullable()->index();
                $table->foreign('vcr_session_id')->references('id')
                    ->on('vcr_sessions')->onDelete('SET NULL');

                $table->softDeletes();
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vcr_session_media');
    }
}
