<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class PostTypeEnum extends Enum
{
    const RECENT =   1;
    const TOP =   2;
    const FOLLOWED =   3;

    const TEXT = 1;
    const TEXT_WITH_IMAGE = 2;
    const TEXT_WITH_IMAGE_AND_FILE = 3;
    const TEXT_WITH_FILE = 4;
    const TEXT_WITH_IMAGE_AND_FILE_AND_VIDEO = 5;
    CONST TEXT_WITH_VIDEO = 6;
}
