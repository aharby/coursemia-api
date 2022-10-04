<?php

namespace App\OurEdu\Courses\UseCases\CourseSubscribeUseCase;

use App\OurEdu\Payments\Enums\PaymentEnums;

interface CourseSubscribeUseCaseInterface
{
    /**
     * @param  int  $courseId
     * @param  int  $studentId
     * @param  bool  $liveSession
     * @return array
     */
    public function subscribeCourse(
        int $courseId,
        int $studentId,
        bool $liveSession = false,
        bool $autoJoin = false,
        string $paymentMethod = PaymentEnums::WALLET
    ): array;
}
