<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentQuestions\MultipleChoice;


use App\OurEdu\Assessments\Models\Questions\MultipleChoice\AssessmentMultipleChoiceOptions;
use App\OurEdu\Assessments\Models\Questions\MultipleChoice\AssessmentMultipleChoiceQuestion;
use App\OurEdu\Assessments\Models\Assessment;

class MultipleChoiceRepository implements MultipleChoiceRepositoryInterface
{

    private $multipleChoiceQuestion;

    public function __construct(AssessmentMultipleChoiceQuestion $multipleChoiceQuestion)
    {
        $this->multipleChoiceQuestion = $multipleChoiceQuestion;
    }

    public function findOrFail(int $id): ?AssessmentMultipleChoiceQuestion
    {
        return $this->multipleChoiceQuestion->findOrFail($id);
    }

    public function findQuestionOrFail(int $id) :? AssessmentMultipleChoiceQuestion{
        return AssessmentMultipleChoiceQuestion::findOrFail($id);
    }

    public function createQuestion($data)
    {
        return $this->multipleChoiceQuestion->create($data);
    }

    public function updateQuestion($questionId, $data)
    {
        $update = $this->multipleChoiceQuestion->where('id', $questionId)->update($data);

        if ($update) {
            return $this->multipleChoiceQuestion->where('id', $questionId)->firstOrFail();
        }
        return null;
    }
    public function insertMultipleOptions($data)
    {
        return AssessmentMultipleChoiceOptions::insert($data);
    }

    public function updateOption($optionsId, $data)
    {
        return AssessmentMultipleChoiceOptions::where('id', $optionsId)->update($data);
    }


    public function getQuestionOptionsIds($questionId)
    {
        $question = $this->multipleChoiceQuestion->find($questionId);

        if ($question) {
            return $question->options()->pluck('id')->toArray();
        }
        return [];
    }

    public function deleteOptions($questionId, $optionsIds)
    {
        $question = $this->multipleChoiceQuestion->find($questionId);

        if ($question) {
            return $question->options()->whereIn('id', $optionsIds)->delete();
        }
        return false;
    }
}
