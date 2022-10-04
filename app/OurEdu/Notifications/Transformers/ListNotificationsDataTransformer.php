<?php


namespace App\OurEdu\Notifications\Transformers;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use League\Fractal\TransformerAbstract;

class ListNotificationsDataTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];

    protected array $availableIncludes = [
    ];

    /**
     * @param $notification
     * @return array
     */
    public function transform($notification)
    {
        $data = $notification->data;
        $returnData= [
            'id' => (string) $notification->id,
            'title' =>  (string) displayTranslation($data['title'], app()->getLocale()),
            'body' =>  (string) displayTranslation($data['body'], app()->getLocale()),
            'url' => $data['url'] ?? null,
            'general_quiz_id' => $data["data"]["general_quiz_id"] ?? null,
            'screen_type' => $data["data"]['screen_type'] ?? null,
            'meeting_type' => $data["data"]['meeting_type'] ?? null, //$data["data"]['meeting_type'] ?? VCRProvidersEnum::AGORA,
            'session_id' => $data["data"]['session_id'] ?? null,
            'vcr_session_id' => $data["data"]['vcr_session_id'] ?? null,
            'vcr_session_type' => $data["data"]['vcr_session_type'] ?? "",
            'read_at' => $notification->read_at ?? null,
            'created_at' => (string) $notification->created_at->diffForHumans(),
        ];

        return $returnData;
    }

    public function includeActions($notification)
    {
        $actions = [];
        if (is_null($notification->read_at)){
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.notifications.mark-notification-read', ['id'=> $notification->id]),
                'label' => trans('notification.Mark as read'),
                'method' => 'GET',
                'key' => APIActionsEnums::MARK_NOTIFICATION_AS_READ
            ];
        }

        if (count($actions)) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

    private function getSystemMeetingType(): string
    {
        $configs = getConfigs();
        $systemMeetingType = $configs['meeting_type'][''] ?? '';

        return in_array($systemMeetingType, VCRProvidersEnum::getList()) ?
            $systemMeetingType :
            VCRProvidersEnum::getDefaultProvider();
    }
}
