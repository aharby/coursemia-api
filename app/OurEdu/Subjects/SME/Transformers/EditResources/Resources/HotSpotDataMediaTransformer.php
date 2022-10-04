<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;

use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotMedia;
use League\Fractal\TransformerAbstract;

class HotSpotDataMediaTransformer extends TransformerAbstract
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
     * @param HotSpotData $hotSpotData
     * @return array
     */
    public function transform(HotSpotMedia $hotSpotData)
    {
        return [
            'id' => $hotSpotData->id,
            'url' => resourceMediaUrl($hotSpotData->filename),
            'filename' => $hotSpotData->filename,
        ];
    }


}

