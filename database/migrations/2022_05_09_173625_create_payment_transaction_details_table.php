<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTransactionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string("payment_transaction_for")->nullable();
        });

        Schema::create('payment_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_transaction_id')
                ->index()->nullable();
            $table->unsignedBigInteger('subscribable_id')
                ->index()
                ->nullable();

            $table->string('subscribable_type')
                ->nullable();
            $table->foreign('payment_transaction_id')
                ->references('id')
                ->on('payment_transactions')
                ->onDelete('SET NULL');

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
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn("payment_transaction_for");
        });

        Schema::dropIfExists('payment_transaction_details');
    }
}
