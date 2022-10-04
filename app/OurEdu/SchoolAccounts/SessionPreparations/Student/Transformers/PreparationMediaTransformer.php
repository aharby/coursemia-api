<?php


namespace App\OurEdu\SchoolAccounts\SessionPreparations\Student\Transformers;


use App\OurEdu\BaseApp\Enums\S3Enums;
use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\SchoolAccounts\SessionPreparations\Models\PreparationMedia;
use League\Fractal\TransformerAbstract;

class PreparationMediaTransformer extends TransformerAbstract
{
    protected array $defaultIncludes =[];
    protected array $availableIncludes =[];


    /**
     * @param PreparationMedia $media
     * @return array
     */
    public function transform(PreparationMedia $media)
    {
        $array= [
            "id" => $media->id,
            'subject_id' => (int)$media->subject_id,
            'mime_type' => (string) $media->mime_type,
            'file_name' => (string) $media->source_filename,
            'url' =>  (string)(getImagePath(S3Enums::LARGE_PATH . $media->filename)),
            'extension' => (string)$media->extension,
            'description' => (string)$media->description,
            'name' => (string) ($media->name ?? $media->source_filename),
        ];
        return array_merge($array,MediaEnums::getTypeExtensionsIconDisplay($media->extension));

    }
}
