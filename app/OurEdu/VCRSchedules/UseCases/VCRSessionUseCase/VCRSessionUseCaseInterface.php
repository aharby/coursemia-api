<?php


namespace App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase;

use App\OurEdu\Payments\Enums\PaymentEnums;

interface VCRSessionUseCaseInterface
{
    public function createSession($requestId, string $paymentMethod = PaymentEnums::WALLET);
    public function rateVCRSession($data, $sessionId);
}
