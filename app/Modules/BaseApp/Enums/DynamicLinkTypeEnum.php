<?php

namespace App\Modules\BaseApp\Enums;

abstract class DynamicLinkTypeEnum
{
    const JOIN_COMPETITION = 'join_competition';
    const ACTIVATE_ACCOUNT = 'activate_account';
    const ACCEPT_INVITATION = 'accept_invitation';
    const GENERAL_EXAM = 'general_exam';
    const ADD_MONEY_TO_WALLET = 'add_money_to_wallet';
    const JOIN_COURSE_COMPETITION = 'join_course_competition' ;
    const RESET_PASSWORD = 'reset_password';
    const VIEW_CHALLNGED_RESULTS = 'view_challnged_results';
    const CHALLENGE_STUDENT = 'challenge_student';

}
