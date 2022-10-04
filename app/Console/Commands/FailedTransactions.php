<?php

namespace App\Console\Commands;

use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\Payments\Gateways\UrWayService\UrWayClient;
use App\OurEdu\Payments\Models\PaymentTransaction;
use App\OurEdu\Payments\Models\UrwayCheckout;
use App\OurEdu\Payments\UseCases\SubmitTransactionUseCase;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FailedTransactions extends Command
{

    /**
     * @var string
     */
    protected $signature = 'payment:check_failed';

    /**
     * @var string
     */
    protected $description = 'payment check_failed';


    /**
     *
     */
    public function __construct(private SubmitTransactionUseCase $submitTransactionUseCase)
    {
        parent::__construct();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        PaymentTransaction::query()
            ->where('status', PaymentEnums::PENDING)
            ->where('created_at', '<=', Carbon::now()->subMinutes(15)->format('Y-m-d H:i:s'))
            ->where('payment_method','!=',PaymentEnums::WALLET)
            ->chunk(30, function ($transactions) {
                foreach ($transactions as $transaction) {
                    try{
                        DB::beginTransaction();
                        if (!empty($transaction->transaction_id)) {
                            $client = new UrWayClient();
                            $client->setTrackId($transaction->id)
                                ->setAmount($transaction->amount)
                                ->setCurrency(env('URWAY_CURRENCY','SAR'))
                                ->setMerchantReference($transaction->merchant_reference)
                                ->setCountry('SA');
                            $returnResponse = $client->find($transaction->transaction_id);
                            $data = [
                                'udf1' => $returnResponse->udf1 ,
                                'responseCode' => $returnResponse->responseCode ?? null,
                                'result' => $returnResponse->result ?? null,
                                'amount' => $returnResponse->amount,
                                'tranid' => $returnResponse->tranid ?? null,
                                'cardBrand' => $returnResponse->cardBrand,
                            ];
                            dump(now()->toDateTimeString() . ": $transaction->transaction_id => $returnResponse->result");
                            if ($returnResponse->result == 'Pending') {
                                continue;
                            }
                            if ($returnResponse->result == 'Successful' || $returnResponse->result == 'Failure' || $returnResponse->result == 'Transaction Expired') {
                                $returnResponse->result = PaymentEnums::FAILED;
                                $this->submitTransactionUseCase->urWayData($data);
                                if ($returnResponse->responseCode == 000) {
                                    $returnResponse->result = PaymentEnums::COMPLETED;
                                    if ($transaction->payment_transaction_for != PaymentEnums::WALLET) {
                                        $this->submitTransactionUseCase->payFor($transaction);
                                    }
                                }
                                $transaction->update([
                                    'status' => $returnResponse->result,
                                    'reviewed'=> 1
                                ]);
                            }
                        }else{
                            $transaction->update([
                                'status' => PaymentEnums::FAILED,
                                'reviewed'=> 1
                            ]);
                        }
                        DB::commit();
                    }catch (\Exception $exception){
                        DB::rollBack();
                        Log::error('Error in getting transaction status for urway', [
                            'error message' => $exception->getMessage(),
                            'in file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                        ]);
                        dump('Error in getting transaction status for urway', [
                            'error message' => $exception->getMessage(),
                            'in file' => $exception->getFile(),
                            'line' => $exception->getLine()
                        ]);
                        continue;
                }
                }
            });
    }
}


