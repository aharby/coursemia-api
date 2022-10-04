<?php


namespace App\OurEdu\LookUp\Transformers;


use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class ClassroomClassSessionLookUpTransformer extends TransformerAbstract
{

    protected array $availableIncludes = [
    ];


    /**
     * @param ClassroomClassSession $session
     * @return array
     */
    public function transform(ClassroomClassSession $session)
    {
        return [
            'id' => (int)$session->id,
            'classroom_id' => (int)$session->classroom_id,
            'subject_id'=> (int)$session->subject_id,
            "day" => $session->from->format('Y-m-d'),
            "from_time" => $session->from->format("H:i"),
            "to_time" => $session->to->format("H:i"),
        ];
    }
}
