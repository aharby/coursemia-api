<?php


namespace App\OurEdu\Certificates\Transformers;

use League\Fractal\TransformerAbstract;
use App\OurEdu\Certificates\Models\ThankingCertificate;


class StudentThankingCertificate extends TransformerAbstract
{

    protected array $defaultIncludes = [];

    protected array $availableIncludes = [
    ];

    public function transform($data)
    {
        return [
            'id'=> $data['certificate'] ? $data['certificate']->id : '',
            'subject_name'=> $data['vcrSession'] ? $data['vcrSession']->subject_name : '',
            'student_name'=> $data['student'] ? $data['student']->name  : '',
            'image' =>  $data['image'] ?? '',
        ];
    }
}
