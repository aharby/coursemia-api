<?php

namespace App\OurEdu\Exams\Enums;

class ExamEnums
{
    /**
     * The idle time to restart exam
     * in minutes
     */
    const IDLE_TIME_TO_END_SESSION = 20;
    const FINISH_EXAM_TOLERANCE_TIME = 60; //in seconds
    const RECOMENDATION_PERCENTAGE = 50;
}
