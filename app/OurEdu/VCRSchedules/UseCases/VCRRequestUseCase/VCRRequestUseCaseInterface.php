<?php

namespace App\OurEdu\VCRSchedules\UseCases\VCRRequestUseCase;

use App\OurEdu\Payments\Enums\PaymentEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;

interface VCRRequestUseCaseInterface
{
    public function request($vcr, $day, $student, $exam = null, string $paymentMethod = PaymentEnums::WALLET);

    public function acceptRequest($requestId);

    public function validateRequestWaitingDuration(VCRSession $session);
}
