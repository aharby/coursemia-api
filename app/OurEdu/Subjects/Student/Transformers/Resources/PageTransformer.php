<?php


namespace App\OurEdu\Subjects\Student\Transformers\Resources;


use App\OurEdu\ResourceSubjectFormats\Models\Page\PageData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
    ];

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

