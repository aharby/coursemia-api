<?php

namespace App\OurEdu\Payments\UseCases;

use App\OurEdu\Payments\Enums\IAPStatus;
use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Models\AppleIAPReceipt;
use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use Illuminate\Support\Facades\Http;

class IAPUseCase
{
    protected PaymentTransactionRepositoryInterface $paymentTransactionRepository;
    private SubmitTransactionUseCaseInterface $submitTransactionUseCase;

    /**
     * @param PaymentTransactionRepositoryInterface $paymentTransactionRepository
     * @param SubmitTransactionUseCaseInterface $submitTransactionUseCase
     */
    public function __construct(
        PaymentTransactionRepositoryInterface $paymentTransactionRepository,
        SubmitTransactionUseCaseInterface $submitTransactionUseCase,
    ) {
        $this->paymentTransactionRepository = $paymentTransactionRepository;
        $this->submitTransactionUseCase = $submitTransactionUseCase;
    }

    public function verify($user, $receipt)
    {
        $receiptResponse = Http::post(
            'https://sandbox.itunes.apple.com/verifyReceipt',
            ["receipt-data" => $receipt]
        )->json();
        if (!IAPStatus::isValid($receiptResponse["status"])) {
            return [
                'status' => '503',
                'detail' => IAPStatus::get($receiptResponse["status"]),
                "title" => "apple_service_error"
            ];
        }
        $trans_ids = [];
        foreach ($receiptResponse["receipt"]["in_app"] as $prod) {
            $trans_ids[] = $prod["transaction_id"];
        }
        $notValid = AppleIAPReceipt::query()
            ->whereIn(
                'transaction_id',
                $trans_ids
            )->exists();
        $prod = $receiptResponse["receipt"]["in_app"][0];
        $transaction = PaymentTransaction::query()->where("sender_id", $user->id)
            ->where('payment_method', PaymentEnums::IAP)
            ->where("status", PaymentEnums::PENDING)->first();
        if (!$transaction) {
            return [
                'status' => '422',
                'detail' => trans('payments.you_have_no_pending_transaction'),
                "title" => "you_have_no_pending_transaction"
            ];
        }
        $iapProduct = $this->paymentTransactionRepository->getIAPProduct($transaction);
        if ($notValid || $prod['product_id'] != $iapProduct->product_id) {
            return [
                'status' => '422',
                'detail' => trans('payments.invalid_receipt'),
                "title" => "invalid_receipt"
            ];
        }
        $iapData = AppleIAPReceipt::create([
            'bundle_id' => $receiptResponse["receipt"]["bundle_id"],
            'transaction_id' => $prod['transaction_id'],
            'product_id' => $prod['product_id'],
            'raw_data' => json_encode($receiptResponse),
            'verified_at' => now(),
        ]);

        $transaction->update([
            'amount' => $iapProduct->price,
            'status' => $receiptResponse["status"] === 0 ? PaymentEnums::COMPLETED : PaymentEnums::FAILED,
            'methodable_id' => $iapData->id,
            'methodable_type' => $iapData::class
        ]);

        $this->submitTransactionUseCase->payFor($transaction);
    }

    public function create($sender, $data)
    {
        $notProcessed = $this->paymentTransactionRepository->hasPendingIAPTransactions($sender);
        if ($notProcessed) {
            return [
                'status' => '422',
                'detail' => trans('payments.you_have_pending_transaction'),
                "title" => "you_have_pending_transaction"
            ];
        }

        $transaction = PaymentTransaction::create([
            'amount' => $data->amount ?? 0,
            'sender_id' => $sender->id,
            'receiver_id' => $data->id,
            'merchant_reference' => $data->merchant_reference,
            'customer_email' => $data->customer_email,
            'customer_ip' => $data->customer_ip,
            'payment_transaction_for' => $data->payment_for,
            "payment_method" => PaymentEnums::IAP
        ]);

        $subscribable = PaymentEnums::PRODUCTS[$data->payment_for] ?? null;
        $transaction->detail()->create([
            'subscribable_id' => $data->payment_for_id,
            'subscribable_type' => $subscribable
        ]);
        $iapProduct = $this->paymentTransactionRepository->getIAPProduct($transaction);

        if (!$iapProduct) {
            $transaction->delete();
            return [
                'status' => '422',
                'detail' => trans('payments.apple_product_not_found'),
                "title" => "apple_product_not_found"
            ];
        }

        return $iapProduct;
    }

    public function cancelSubscription($user)
    {
        $transaction = PaymentTransaction::query()->where("sender_id", $user->id)
            ->where('payment_method', PaymentEnums::IAP)
            ->where("status", PaymentEnums::PENDING)->first();

        if (!$transaction) {
            return [
                'status' => '422',
                'detail' => trans('payments.you_have_no_pending_transaction'),
                "title" => "you_have_no_pending_transaction"
            ];
        }

        return $transaction->update(['status' => PaymentEnums::CANCELLED]);
    }
}
