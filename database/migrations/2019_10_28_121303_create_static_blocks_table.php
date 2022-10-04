<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaticBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('static_blocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->unique();
            $table->tinyInteger('is_active')->default(0);

            $table->longText('url')->nullable();
            $table->longText('bg_image')->nullable();
            $table->string('icon')->nullable();

            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('static_blocks')->onDelete('SET NULL');

            $table->bigInteger('page_id')->unsigned();
            $table->foreign('page_id')->references('id')->on('static_pages')->onDelete('CASCADE');

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
        Schema::dropIfExists('static_blocks');
    }
}
