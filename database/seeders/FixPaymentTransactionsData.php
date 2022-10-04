<?php

namespace Database\Seeders;

use App\OurEdu\Payments\Models\PaymentTransaction;
use Illuminate\Database\Seeder;

class FixPaymentTransactionsData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transactions = PaymentTransaction::query()
            ->where('sender_id', '=', null)
            ->get();
        if (count($transactions)) {
            foreach ($transactions as $transaction) {
                $transaction->update([
                    'sender_id' => $transaction->receiver_id
                ]);
            }
        }
    }
}
