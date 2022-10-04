<?php

namespace App\OurEdu\Exams\Events\CompetitionEvents;

use App\OurEdu\BaseApp\Api\Traits\ApiResponser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Events\Contracts\StudentStoredEventContract;
use App\OurEdu\Exams\Student\Transformers\Competitions\Events\StudentAnsweredCompetitionQuestionTransformer;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class StudentAnsweredCompetitionQuestion extends ShouldBeStored implements  StudentStoredEventContract , ShouldBroadcast
{
    use SerializesModels, Dispatchable , ApiResponser;

    /**
     * @var array
     */
    private $competitionId;
    private $questionId;
    private $questionCorrectRatio;
    private $questionNotCorrectRatio;


    /**
     * Create a new event instance.
     *
     * @param int $competitionId
     * @param int $questionId
     * @param int $questionCorrectRatio
     * @param int $questionNotCorrectRatio
     */
    public function __construct($competitionId,
                                $questionId,
                                $questionCorrectRatio,
                                $questionNotCorrectRatio)
    {
        $this->competitionId = $competitionId;
        $this->questionId = $questionId;
        $this->questionCorrectRatio = $questionCorrectRatio;
        $this->questionNotCorrectRatio = $questionNotCorrectRatio;
    }

    public function broadcastOn(){

        return new PresenceChannel('competition.'. $this->competitionId);
    }

    public function broadcastAs() {

        return 'CompetitionQuestionAnswered';
    }

    public function broadcastWith()
    {
        $data = [
            'competitionId' => $this->competitionId,
            'questionId' => $this->questionId,
            'questionCorrectRatio' => $this->questionCorrectRatio,
            'questionNotCorrectRatio' => $this->questionNotCorrectRatio,
        ];

        return $this->transformDataMod($data , new StudentAnsweredCompetitionQuestionTransformer() , ResourceTypesEnums::STUDENT_ANSWERED_COMPETITION_QUESTION);
    }
}
