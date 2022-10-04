<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;

use App\OurEdu\ResourceSubjectFormats\Models\Video\VideoDataMedia;
use League\Fractal\TransformerAbstract;

class VideoDataMediaTransformer extends TransformerAbstract
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
     * @param VideoDataMedia $videoDataMedia
     * @return array
     */
    public function transform(VideoDataMedia $videoDataMedia)
    {
        return [
            'id' => $videoDataMedia->id,
            'url' => resourceMediaUrl($videoDataMedia->filename),
            'filename' => $videoDataMedia->filename,
        ];
    }


}

