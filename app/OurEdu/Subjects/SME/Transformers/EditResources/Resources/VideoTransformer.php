<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use League\Fractal\TransformerAbstract;

class VideoTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'videoDataMedia'
    ];
    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param VideoData $videoData
     * @return array
     */
    public function transform(VideoData $videoData)
    {
        return [
            'id' => $videoData->id,
            'title' => $videoData->title,
            'description' => $videoData->description,
            'video' => $videoData->video_type == 'url' ? $videoData->link : null,
            'video_type' => $videoData->video_type

        ];
    }

    public function includeVideoDataMedia(VideoData $videoData)
    {
//        dd($videoData->media()->first());
        if ($videoData->media()->exists()) {
            return $this->item($videoData->media()->first(), new VideoDataMediaTransformer(), ResourceTypesEnums::VIDEO_DATA_MEDIA);
        }
    }
}
