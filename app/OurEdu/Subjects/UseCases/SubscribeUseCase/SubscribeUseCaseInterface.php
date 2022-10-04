<?php

namespace App\OurEdu\Subjects\UseCases\SubscribeUseCase;

use App\OurEdu\Payments\Enums\PaymentEnums;

interface SubscribeUseCaseInterface
{
    /**
     * @param int $subjectId
     * @param int $studentId
     * @return array
     */
    public function subscribeSubject(
        int $subjectId,
        int $studentId,
        string $paymentMethod = PaymentEnums::WALLET
    ): array;
}
