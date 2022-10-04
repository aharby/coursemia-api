<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResEssayDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('res_essay_data', function (Blueprint $table) {
            $table->id();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('resource_subject_format_subject_id')->nullable()->index();
            $table->foreign('resource_subject_format_subject_id',
                'resource_subject_format_subject_id_essay_fr')
                ->references('id')->on('resource_subject_format_subject')->onDelete('SET NULL');

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
        Schema::dropIfExists('res_essay_data');
    }
}
