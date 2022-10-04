<?php

namespace App\OurEdu\Payments\Enums;

use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\VCRSchedules\Models\VCRRequest;

abstract class PaymentEnums
{
    public const FAILED = 'Failed',
        COMPLETED = 'Completed',
        CANCELLED = 'Cancelled',
        PENDING = 'Pending',
        COURSE = 'course',
        SUBJECT = 'subject',
        VCR_SPOT = 'vcr_spot',
        VCR_SUBJECT = 'vcr_subject',
        VISA = 'visa',
        WALLET = 'wallet',
        IAP = 'iap',
        ADD_MONEY_WALLET = 'add_money_to_wallet',
        EXAM_VCR = 'exam vcr';


    //payment products
    public const PRODUCTS = [
        self::COURSE => Course::class,
        self::SUBJECT => Subject::class,
        self::VCR_SPOT => VCRRequest::class,
        self::VCR_SUBJECT => VCRRequest::class,
    ];

    //payment products
    public const PRODUCTS_MAP = [
        self::COURSE => Course::class,
        self::SUBJECT => Subject::class,
        self::VCR_SPOT => VCRRequest::class,
        self::VCR_SUBJECT => VCRRequest::class,
        self::EXAM_VCR => Exam::class,
    ];

    public static function getProducts(): array
    {
        return [
            self::COURSE => trans("payments." . self::COURSE),
            self::SUBJECT => trans("payments." . self::SUBJECT),
            self::VCR_SPOT => trans("payments." . self::VCR_SPOT),
            self::ADD_MONEY_WALLET => trans("payments." . self::ADD_MONEY_WALLET)
        ];
    }


    //In-app purchase rows
    public const COURSE_OFFSET = 0;
    public const SUBJECT_OFFSET = 100;
}
