<?php

namespace App\OurEdu\Payments\Gateways;

use App\OurEdu\Payments\Gateways\UrWayService\UrWayClient;
use App\OurEdu\Payments\Repository\PaymentTransactionRepositoryInterface;
use App\OurEdu\Payments\UseCases\SubmitTransactionUseCaseInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class UrWayGateway implements PaymentGateway
{

    public function __construct(
        private PaymentTransactionRepositoryInterface $transactionRepository,
        private SubmitTransactionUseCaseInterface $submitTransactionUseCase
    ) {
    }


    /**
     * @param array $requestData
     * @return array
     * @throws Exception
     */
    public function frameData(array $requestData)
    {
        $transaction = $this->transactionRepository->findBy('merchant_reference', $requestData['merchant_reference']);

        $data = [
            'sender_name' => $transaction->sender->first_name,
            'merchant_reference' => $requestData['merchant_reference'],
            'amount' => $transaction->amount,
            'currency' => config('urway.currency'),
            'merchant_extra' => $transaction->uuid,
            'customer_email' => $transaction->customer_email,
            'language' => app()->getLocale(),
            'customer_ip' => $transaction->customer_ip,
            'track_id' => $transaction->id           // track_id is the transaction id in our side
        ];
        $returnData = $this->paymentRequest($data);

        $transaction->transaction_id = $returnData->payid ?? null; // transaction_id is the transaction id in urway side
        $transaction->save();

        $data['url'] = $returnData->getPaymentUrl();
        return $data;
    }

    /**
     * @throws Exception
     */
    private function paymentRequest($data)
    {
        $request = new UrWayClient();
        $request->setTrackId($data['track_id'])
            ->setCustomerEmail($data['customer_email'])
            ->setCurrency($data['currency'])
            ->setCountry('SA')
            ->setAmount($data['amount'])
            ->setAttribute("udf3", $data['language'])
            ->setAttribute('udf1', $data['merchant_reference'] ?? null)
            ->setAttribute('First_name', $data['sender_name'])
            ->setAttribute('udf2', $data['merchant_extra1'] ?? null)
            ->setAttribute('udf4', $data['merchant_extra2'] ?? null)
            ->setRedirectUrl(route('payment.response'))
            ->setCustomerIp($data['customer_ip']);

        $response = $request->pay();
        return $response;
    }

    /**
     * @param array $responseData
     * @return mixed
     * @throws Exception
     */
    public function response(array $responseData)
    {
        $response = $this->checkResponseStatus($responseData);

        return $this->submitTransactionUseCase->urWayData($response->all());
    }

    /**
     * @throws Exception
     */
    protected function checkResponseStatus(array $request)
    {
        $client = new UrWayClient();
        $client->setTrackId($request['TrackId']);
        $client->setAmount($request['amount'])
            ->setCustomerEmail($request['email'])
            ->setCurrency(config('urway.currency'))
            ->setCountry('SA');
        return $client->find($request['TranId']);
    }
}
