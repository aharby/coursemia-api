<?php

use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentTransactionTableAddPaymentMethodCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->enum('payment_method', [PaymentEnums::WALLET, PaymentEnums::VISA])
                ->after('status')
                ->default(PaymentEnums::VISA)
                ->comment('visa or wallet');
            $table->enum('payment_transaction_type',
                [TransactionTypesEnums::DEPOSIT, TransactionTypesEnums::WITHDRAWAL, TransactionTypesEnums::REFUND]
            )->default(TransactionTypesEnums::DEPOSIT)
                ->comment('deposit , withdrawal or refund');
            $table->unsignedBigInteger('parent_payment_transaction_id')->nullable();
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
            $table->dropColumn('payment_method');
        });
    }
}
