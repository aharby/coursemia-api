<?php

namespace App\OurEdu\GeneralQuizzes\Student\Transformers;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudent;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class GeneralQuizTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'subject'
    ];

    public function transform(GeneralQuiz $generalQuiz): array
    {
        $generalQuizStudent = $generalQuiz->studentsAnswered->first();

        return [
            'id' => $generalQuiz->id,
            'quiz_title' => $generalQuiz->title,
            'quiz_type' => GeneralQuizTypeEnum::getLabel($generalQuiz->quiz_type ?? ""),
            'start_date' => $generalQuiz->start_at,
            'end_date' => $generalQuiz->end_at,
            'result' => ($generalQuizStudent->score ?? 0) . "/" . $generalQuiz->mark
        ];
    }

    public function includeSubject(GeneralQuiz $generalQuiz): ?Item
    {
        $subject = $generalQuiz->subject;

        if ($subject) {
            return $this->item($subject, new SubjectTransformer(), ResourceTypesEnums::SUBJECT);
        }

        return null;
    }
}
