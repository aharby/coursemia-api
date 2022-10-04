<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentQuestions\RatingQuestion;


use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingQuestion;
use App\OurEdu\Assessments\Models\Questions\Rating\AssissmentRatingOptions;

class RatingQuestionRepository implements RatingQuestionRepositoryInterface
{

    private $assissmentRatingQuestion;

    public function __construct(AssissmentRatingQuestion $assissmentRatingQuestion)
    {
        $this->assissmentRatingQuestion = $assissmentRatingQuestion;
    }

    public function findOrFail(int $id): ?AssissmentRatingQuestion
    {
        return $this->assissmentRatingQuestion->findOrFail($id);
    }

    public function findQuestionOrFail(int $id) :? AssissmentRatingQuestion{
        return AssissmentRatingQuestion::findOrFail($id);
    }

    public function createQuestion($data)
    {
        return $this->assissmentRatingQuestion->create($data);
    }

    public function updateQuestion($questionId, $data)
    {
        $update = $this->assissmentRatingQuestion->where('id', $questionId)->update($data);

        if ($update) {
            return $this->assissmentRatingQuestion->where('id', $questionId)->firstOrFail();
        }
        return null;
    }
    public function insertMultipleOptions($data)
    {
        return AssissmentRatingOptions::insert($data);
    }

    public function updateOption($optionsId, $data)
    {
        return AssissmentRatingOptions::where('id', $optionsId)->update($data);
    }


    public function getQuestionOptionsIds($questionId)
    {
        $question = $this->assissmentRatingQuestion->find($questionId);

        if ($question) {
            return $question->options()->pluck('id')->toArray();
        }
        return [];
    }

    public function deleteOptions($questionId, $optionsIds)
    {
        $question = $this->assissmentRatingQuestion->find($questionId);

        if ($question) {
            return $question->options()->whereIn('id', $optionsIds)->delete();
        }
        return false;
    }
}
