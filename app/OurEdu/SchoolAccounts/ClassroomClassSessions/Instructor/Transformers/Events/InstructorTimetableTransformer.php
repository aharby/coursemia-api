<?php


namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\Events;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class InstructorTimetableTransformer extends TransformerAbstract
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
            'branchId' => $data['branchId'],
            'vcrId' => $data['vcrId'],
            'sessionId' => $data['sessionId'],
            'meetingType' => $data['meetingType'],
        ];

        return $transformerData;
    }
}
