<?php

namespace Database\Seeders;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Enums\TransactionTypesEnums;
use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Payments\Models\PaymentTransactionDetails;
use App\OurEdu\Payments\Models\Transaction;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionsToPaymentTransactionsTransformerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            dump("Start Transforming Transactions Data To Payment Transactions....");
            DB::beginTransaction();
            dump("cloning transactions data");
            $oldTransactions = Transaction::query()
                ->whereHas('user')
                ->whereHas('subscribable')
                ->get();
            foreach ($oldTransactions as $transaction) {
                if (isset($transaction->subscribable->id)) {
                    $isVisaTransaction = PaymentTransaction::query()
                        ->where('receiver_id', $transaction->user_id)
                        ->where('sender_id', null)
                        ->whereHas('detail', function ($detail) use ($transaction) {
                            $detail->where('subscribable_id', $transaction->subscribable_id)
                                ->where('subscribable_type', $transaction->subscribable_type);
                        })->first();
                    if (!$isVisaTransaction) {
                        switch ($transaction->subscribable_type) {
                            case Subject::class:
                                $paymentTransactionFor = PaymentEnums::SUBJECT;
                                break;
                            case Course::class:
                                $paymentTransactionFor = PaymentEnums::COURSE;
                                break;
                            default:
                                $paymentTransactionFor = PaymentEnums::VCR_SPOT;
                        }

                        $payment = PaymentTransaction::create([
                            'receiver_id' => $transaction->user_id,
                            'amount' => $transaction->amount,
                            'payment_method' => PaymentEnums::WALLET,
                            'payment_transaction_type' => TransactionTypesEnums::WITHDRAWAL,
                            'status' => PaymentEnums::COMPLETED,
                            'payment_transaction_for' => $paymentTransactionFor,
                            'created_at' => $transaction->created_at,
                            'updated_at' => $transaction->updated_at,
                            'deleted_at' => $transaction->deleted_at
                        ]);
                        $payment->detail()->create([
                            'subscribable_id' => $transaction->subscribable_id,
                            'subscribable_type' => $transaction->subscribable_type,
                            'created_at' => $transaction->created_at,
                            'updated_at' => $transaction->updated_at
                        ]);
                    }
                }
            }
            dump("cloning transactions data done successfully");
            DB::commit();
        } catch (\Exception $exception) {
            dump(
                "Ooops,something went wrong..",
                "the exception is",
                $exception->getMessage(),
                "in",
                $exception->getFile(),
                "line",
                $exception->getLine()
            );
            DB::rollBack();
        }
    }
}
