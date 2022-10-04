<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUrwayCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urway_checkouts', function (Blueprint $table) {
            $table->id();
            $table->string('udf1')->nullable();
            $table->unsignedMediumInteger('response_code')->nullable();
            $table->string('response_message')->nullable();
            $table->integer('amount')->nullable();
            $table->string('card_number',19)->nullable();
            $table->string('payment_option',10)->nullable();
            $table->text('raw_response')->nullable();
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
        Schema::dropIfExists('urway_checkouts');
    }
}
