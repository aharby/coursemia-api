<?php


namespace App\OurEdu\Notifications\Transformers;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class ListNotificationsTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'notificationsData'
    ];

    protected array $availableIncludes = [
    ];

    /**
     * @param $holderObject
     * @return array
     */
    public function transform($holderObject)
    {

        $returnData= [
            'id' => (string) Str::uuid(),
            'notifications_count' => (int) $holderObject->notifications_count,
        ];

        return $returnData;
    }

    public function IncludeNotificationsData($holderObject)
    {
        return $this->collection($holderObject->notificationsData, new ListNotificationsDataTransformer(), ResourceTypesEnums::NOTIFICATION);
    }

}
