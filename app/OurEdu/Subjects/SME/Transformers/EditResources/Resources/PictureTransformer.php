<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Picture\PictureData;
use League\Fractal\TransformerAbstract;

class PictureTransformer extends TransformerAbstract
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
            return $this->collection($pictureData->media, new PictureDataMediaTransformer(), ResourceTypesEnums::RESOURCE_DATA_MEDIA);
        }
    }
}
