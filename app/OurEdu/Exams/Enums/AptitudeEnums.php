<?php


namespace App\OurEdu\Exams\Enums;

class AptitudeEnums
{
    public const TOTAL_NUMBER_OF_QUESTIONS = 120;
    public const QUANTITATIVE_SECTION = 'quantitative';
    public const VERBAL_SECTION = 'verbal';
    public const APTITUDE_PERCENTAGES = [
        'math' => 36,
        'geometry' => 18,
        'algebra' => 10,
        'statistics' => 18,
        'comparison' => 18,

        'read_comprehension' => 30,
        'complete_sentences' => 20,
        'verbal_symmetry' => 20,
        'remaining_error' => 15,
        'correlation_and_variation' => 15,

    ];
}
