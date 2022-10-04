<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoData;
use League\Fractal\TransformerAbstract;

class VideoDataTransformer extends TransformerAbstract
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

    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
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
            'video_type' => $videoData->حاح

        ];
    }

    public function includeVideoDataMedia(VideoData $videoData)
    {
//        dd($videoData->media()->first());
        if ($videoData->media()->exists()) {
            return $this->item($videoData->media()->first(),new VideoDataMediaTransformer(),ResourceTypesEnums::VIDEO_DATA_MEDIA);
        }
    }


}

