<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class StatusCodesEnum extends Enum
{
    const DONE =   1;

    const FAILED =   2;

    const UNAUTHORIZED = 3;

    const PHONE_NUMBER_NOT_VERIFIED = 4;

    const EMAIL_NOT_VERIFIED = 5;

    const PHONE_NUMBER_AND_EMAIL_NOT_VERIFIED = 6; 

    const PHONE_NUMBER_ALREADY_EXISTS = 7;

    const EMAIL_ALREADY_EXISTS = 8;

}
