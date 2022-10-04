<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use League\Fractal\TransformerAbstract;

class FlashDataTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'flashDataMedia'
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
     * @param FlashData $flashData
     * @return array
     */
    public function transform(FlashData $flashData)
    {
        return [
            'id' => $flashData->id,
            'title' => $flashData->title,
            'description' => $flashData->description,
        ];
    }

    public function includeFlashDataMedia(FlashData $flashData)
    {
        if ($flashData->media()->exists()) {
            return $this->item($flashData->media()->first(),new FlashDataMediaTransformer(),ResourceTypesEnums::RESOURCE_DATA_MEDIA);
        }
    }


}

