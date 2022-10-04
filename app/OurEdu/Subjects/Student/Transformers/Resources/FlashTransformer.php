<?php


namespace App\OurEdu\Subjects\Student\Transformers\Resources;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class FlashTransformer extends TransformerAbstract
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
            return $this->item($flashData->media()->first(), new FlashDataMediaTransformer(), ResourceTypesEnums::RESOURCE_DATA_MEDIA);
        }
    }
}
