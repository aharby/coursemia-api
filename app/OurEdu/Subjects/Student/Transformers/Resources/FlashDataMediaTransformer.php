<?php


namespace App\OurEdu\Subjects\Student\Transformers\Resources;

use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashData;
use App\OurEdu\ResourceSubjectFormats\Models\Flash\FlashDataMedia;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class FlashDataMediaTransformer extends TransformerAbstract
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
     * @param FlashData $flashDataMedia
     * @return array
     */
    public function transform(FlashDataMedia $flashDataMedia)
    {
        return [
            'id' => Str::uuid(),
            'url' => resourceMediaUrl($flashDataMedia->filename),
            'filename' => $flashDataMedia->filename,
        ];
    }


}

