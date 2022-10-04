<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Models\ClassroomClassSessionScores;
use League\Fractal\TransformerAbstract;

class SessionScoreTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [

    ];


    /**
     * @param ClassroomClassSessionScores $score
     * @return array
     */
    public function transform(ClassroomClassSessionScores $score)
    {
        return [
            'id' => (int)$score->id,
            'score_type' => (string)$score->score_type,
            'score' => (int)$score->score,
        ];
    }

}
