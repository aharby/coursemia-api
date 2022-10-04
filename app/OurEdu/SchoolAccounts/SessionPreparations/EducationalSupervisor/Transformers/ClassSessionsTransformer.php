<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\EducationalSupervisor\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ClassSessionsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
        "session_preparations",
    ];


    /**
     * @param ClassroomClassSession $session
     * @return array
     */
    public function transform(ClassroomClassSession $session)
    {
        return [
            "id" => (int)$session->id,
            "classroom_id" => (int)$session->classroom_id,
            "subject_id" => (int)$session->subject_id,
            "day" => $session->from->format("Y-m-d"),
            "from_time" => $session->from->format("H:i"),
            "to_time" => $session->to->format("H:i"),
        ];
    }


    /**
     * @param ClassroomClassSession $session
     * @return Item
     */
    public function includeSessionPreparations(ClassroomClassSession $session)
    {
        $sessionPreparation = $session->preparation;
        if ($sessionPreparation) {
            return $this->item($sessionPreparation, new SessionPreparationsTransformer(), ResourceTypesEnums::PREPARATION);
        }
    }

    public function includeActions(ClassroomClassSession $session)
    {
        $actions = [];
        if ($vcrSession = $session->vcrSession) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.online-sessions.getSessionFiles', ['sessionId' => $vcrSession->id]),
                'label' => trans('api.SessionMedia'),
                'method' => 'GET',
                'key' => APIActionsEnums::VCR_SESSION_MEDIA
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }
}
