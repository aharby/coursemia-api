<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class RolesEnum extends Enum
{
    const SUPER_ADMIN = 'super-admin';
    const ADMIN = 'admin';
    const INSTRUCTOR = 'instructor';
    const ASSISTANT = 'assistant';
    const STUDENT = 'student';  
    const GUEST = 'guest';
}
