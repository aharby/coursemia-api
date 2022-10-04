<?php

namespace App\OurEdu\Payments\Gateways;

use App\OurEdu\Payments\UseCases\SubmitTransactionUseCaseInterface;

class PayfortGateway implements PaymentGateway
{


    public function __construct(private SubmitTransactionUseCaseInterface $submitTransactionUseCase)
    {
    }

    /**
     * @param array $requestData
     * @return array
     */
    public function frameData(array $requestData)
    {
        return $this->submitTransactionUseCase->prepareToken($requestData['merchant_reference']);
    }

    /**
     * @param array $responseData
     * @return mixed
     */
    public function response(array $responseData)
    {
        return $this->submitTransactionUseCase->payfortData($responseData);
    }
}
