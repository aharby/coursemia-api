<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotSpotDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_hot_spot_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('description')->nullable();

            $table->unsignedBigInteger('resource_subject_format_subject_id')->nullable()->index();
            $table->foreign('resource_subject_format_subject_id', 'resource_subject_format_subject_id_hot_spot_fr')->references('id')->on('resource_subject_format_subject')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_hot_spot_data');
    }
}
