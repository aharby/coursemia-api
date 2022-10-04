<?php


namespace App\OurEdu\GeneralQuizzes\Subject\Transformers;


use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use League\Fractal\TransformerAbstract;

class SubjectFormatSubjectTransformer extends TransformerAbstract
{
    public function transform(SubjectFormatSubject $subjectFormatSubject) {
        return [
            'id' => (int)$subjectFormatSubject->id,
            'title' => (string)$subjectFormatSubject->title,
            'has_sub_sections'=>(boolean)$subjectFormatSubject->childSubjectFormatSubject()->count()>0?true:false,
            'has_parent' => is_null($subjectFormatSubject->parent_subject_format_id),
        ];
    }
}
