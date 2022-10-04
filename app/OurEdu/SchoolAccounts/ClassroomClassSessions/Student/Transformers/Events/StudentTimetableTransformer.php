<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Transformers\Events;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class StudentTimetableTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [

    ];
    protected array $availableIncludes = [
    ];
    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function transform($data)
    {
        $transformerData = [
            'id' => Str::uuid(),
            'classroomId' => $data['classroomId'],
            'vcrId' => $data['vcrId'],
            'sessionId' => $data['sessionId'],
            'meetingType' => $data['meetingType'],
        ];

        return $transformerData;
    }
}
