<?php


namespace App\OurEdu\ResourceSubjectFormats\Transformers;


use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Transformers\LearningResourceAcceptCriteriaGetTransformer;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\Page\PageData;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class PageDataTransformer extends TransformerAbstract
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

    /**
     * @param PageData $pageData
     * @return array
     */
    public function transform(PageData $pageData)
    {


        return [
            'id' => $pageData->id,
            'title' => $pageData->title,
            'page' => $pageData->page,

        ];
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


}

