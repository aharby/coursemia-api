<?php

namespace App\OurEdu\Payments\Gateways;

interface PaymentGateway
{
    public function frameData(array $requestData);

    public function response(array $responseData);
}
