<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayfortCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payfort_checkouts', function (Blueprint $table) {
            $table->id();
            $table->string('token_name')->nullable();
            $table->unsignedMediumInteger('response_code');
            $table->string('response_message');
            $table->integer('amount');
            $table->string('currency',3);
            $table->string('card_number',19);
            $table->string('card_holder_name',50);
            $table->string('payment_option',10);
            $table->string('expiry_date',4);
            $table->string('fort_id',20);
            $table->string('customer_email');
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
        Schema::dropIfExists('payfort_checkouts');
    }
}
