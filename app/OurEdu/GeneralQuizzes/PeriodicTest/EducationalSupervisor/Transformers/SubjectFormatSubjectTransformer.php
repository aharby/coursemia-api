<?php


namespace App\OurEdu\GeneralQuizzes\PeriodicTest\EducationalSupervisor\Transformers;


use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use League\Fractal\TransformerAbstract;

class SubjectFormatSubjectTransformer extends TransformerAbstract
{
    public function transform(SubjectFormatSubject $subjectFormatSubject) {
        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'description' => (string)$subjectFormatSubject->description,
            'has_sub_sections'=>(boolean)$subjectFormatSubject->childSubjectFormatSubject()->count()>0?1:0,
        ];
    }
}
