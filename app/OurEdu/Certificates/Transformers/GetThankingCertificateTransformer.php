<?php

namespace App\OurEdu\Certificates\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\Certificates\Models\ThankingCertificate;

class GetThankingCertificateTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [
    ];

    public function transform(ThankingCertificate $certificates)
    {
        return [
            'id'=> $certificates->id,
            'image' => $certificates->image,
        ];
    }
}
