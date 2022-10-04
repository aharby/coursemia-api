<?php

use App\OurEdu\Payments\Enums\PaymentEnums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentMethodInPaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE payment_transactions
                   MODIFY COLUMN payment_method ENUM('wallet', 'visa', 'iap')"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(
            "ALTER TABLE payment_transactions
                   MODIFY COLUMN payment_method ENUM('wallet', 'visa')"
        );
    }
}
