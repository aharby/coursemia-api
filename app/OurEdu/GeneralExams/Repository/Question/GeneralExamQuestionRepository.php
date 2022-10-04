<?php

namespace App\OurEdu\GeneralExams\Repository\Question;

use App\OurEdu\GeneralExams\Models\GeneralExamQuestion;
use App\OurEdu\GeneralExams\Models\GeneralExamStudentAnswer;

class GeneralExamQuestionRepository implements GeneralExamQuestionRepositoryInterface
{
    public function create($data)
    {
        return GeneralExamQuestion::create($data);
    }

    public function createOptions($question, $options)
    {
        return $question->options()->createMany($options);
    }

    public function createMedia($question, $preparedQuestion)
    {
        if ($preparedQuestion->questionable) {
            $media = $preparedQuestion->questionable->media;
        }else {
            $media = $preparedQuestion->media;
        }
        $question->media()->create([
            'source_filename'   =>  $media->source_filename,
            'filename'   =>  $media->filename,
            'mime_type'   =>  $media->mime_type,
            'url'   =>  $media->url,
            'extension'   =>  $media->extension,
            'status'   =>  $media->status
        ]);
    }

    public function findExamQuestion($exam, $questionId)
    {
        return $exam->questions()->where('id', $questionId)->with('options')->firstOrFail();
    }

    public function updateOrCreateAnswer($question, $data)
    {
        if ($answer = $question->studentAnswers()->where('student_id', $data['student_id'])->first()) {
            $answer->update($data);
        } else {
            $answer = GeneralExamStudentAnswer::create($data);
        }


        if (isset($data['details'])) {
            foreach ($data['details'] as $detail) {
                $answer->details()->firstOrcreate($detail);
            }
        }

        return $answer;
    }
}
