<?php


namespace App\OurEdu\Subjects\Student\Transformers;

use App\OurEdu\BaseApp\Enums\S3Enums;
use App\OurEdu\Subjects\Models\SubModels\SubjectMedia;
use Illuminate\Support\Facades\Storage;
use League\Fractal\TransformerAbstract;

class SubjectMediaTransformer extends TransformerAbstract
{

    protected array $defaultIncludes = [

    ];
    protected array $availableIncludes = [

    ];


    /**
     * @param SubjectMedia $subjectMedia
     * @return array
     */
    public function transform(SubjectMedia $subjectMedia)
    {

        return [
            'id' => (int)$subjectMedia->id,
            'mime_type' => (string) $subjectMedia->mime_type,
            'file_name' => (string) $subjectMedia->source_filename,
            'url' =>  (string)(getImagePath(S3Enums::LARGE_PATH . $subjectMedia->filename)),
            'extension' => (string)$subjectMedia->extension
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

