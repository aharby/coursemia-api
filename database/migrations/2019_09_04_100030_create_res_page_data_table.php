<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResPageDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_page_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->longText('page')->nullable();

            $table->unsignedBigInteger('resource_subject_format_subject_id')->nullable()->index();
            $table->foreign('resource_subject_format_subject_id', 'resource_subject_format_subject_id_page_fr')->references('id')->on('resource_subject_format_subject')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_page_data');
    }
}
