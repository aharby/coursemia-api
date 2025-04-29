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
    const UNVERIFIED = 4;

    const EMAIL_OR_PHONE_ALREADY_EXISTS = 5;
}
