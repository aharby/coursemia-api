<?php


namespace App\OurEdu\Reports\Parent\Transformers;


use App\OurEdu\Users\User;
use League\Fractal\TransformerAbstract;

class InstructorTransformer extends TransformerAbstract
{
    public function transform(User $instructor) {
        return [
            "id" => $instructor->id,
            "first_name" => $instructor->first_name,
            "last_name" => $instructor->last_name,
        ];
    }
}
