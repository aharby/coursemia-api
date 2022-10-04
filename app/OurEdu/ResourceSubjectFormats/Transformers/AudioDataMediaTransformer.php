<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;

use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioData;
use App\OurEdu\ResourceSubjectFormats\Models\Audio\AudioDataMedia;
use League\Fractal\TransformerAbstract;

class AudioDataMediaTransformer extends TransformerAbstract
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
     * @param AudioData $audioDataMedia
     * @return array
     */
    public function transform(AudioDataMedia $audioDataMedia)
    {
        return [
            'id' => $audioDataMedia->id,
            'url' => resourceMediaUrl($audioDataMedia->filename),
            'filename' => $audioDataMedia->filename,
        ];
    }


}

