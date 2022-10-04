<?php


namespace App\OurEdu\Notifications\Transformers;


use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
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
            'title' =>  (string) displayTranslation($data['title']),
            'body' =>  (string) displayTranslation($data['body']),
            'url' => $data['url'] ?? null,
            'screen_type' => $data['screen_type'] ?? null,
            'read_at' => $notification->read_at ?? null,
            'created_at' => (string) $notification->created_at->diffForHumans(),
        ];

        return $returnData;
    }
}
