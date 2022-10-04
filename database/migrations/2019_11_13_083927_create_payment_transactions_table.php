<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sender_id')
                ->index()->nullable();
            $table->unsignedBigInteger('receiver_id')
                ->index()->nullable();
            $table->string('amount');

            $table->unsignedBigInteger('methodable_id')
                ->index()
                ->nullable();

            $table->string('methodable_type')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sender_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');

            $table->foreign('receiver_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_transactions');
    }
}
