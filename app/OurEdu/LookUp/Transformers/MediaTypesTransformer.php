<?php


namespace App\OurEdu\LookUp\Transformers;


use League\Fractal\TransformerAbstract;

class MediaTypesTransformer extends TransformerAbstract
{
    public function transform(string $mediaType)
    {
        return [
            'id' => $mediaType,
            'name' => $mediaType
        ];
    }
}
