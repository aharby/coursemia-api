<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;
use League\Fractal\TransformerAbstract;

class PictureDataTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'pictureDataMedia'
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
     * @param PictureData $pictureData
     * @return array
     */
    public function transform(PictureData $pictureData)
    {
        return [
            'id' => $pictureData->id,
            'title' => $pictureData->title,
            'description' => $pictureData->description,
        ];
    }

    public function includePictureDataMedia(PictureData $pictureData)
    {
        if ($pictureData->media()->exists()) {
            return $this->collection($pictureData->media,new PictureDataMediaTransformer(),ResourceTypesEnums::RESOURCE_DATA_MEDIA);
        }
    }


}

