<?php



namespace App\OurEdu\GeneralQuizzes\PeriodicTest\Instructor\Transformers;


use App\OurEdu\Subjects\Models\Subject;

use League\Fractal\TransformerAbstract;


class SubjectTransformer extends TransformerAbstract

{

    public function transform(Subject $subject)

    {

        return [

            'id' => (int)$subject->id,

            'name' => (string)$subject->name,

        ];

    }

}