<?php


namespace App\OurEdu\Assessments\Repositories\AssessmentQuestions\EassyQuestions;


use App\OurEdu\Assessments\Models\Questions\Essay\AssessmentEssayQuestion;

class AssessmentEssayQuestionRepository implements AssessmentEssayQuestionRepositoryInterface
{

    /**
     * @param int $id
     * @return AssessmentEssayQuestion|\Illuminate\Database\Eloquent\Model|null
     */
    public function findOrFail(int $id): ?AssessmentEssayQuestion
    {
        return AssessmentEssayQuestion::query()->findOrFail($id);
    }

    /**
     * @param array $data
     * @return AssessmentEssayQuestion|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): AssessmentEssayQuestion
    {
        $essayQuestion = AssessmentEssayQuestion::query()->create($data);

        return $essayQuestion;
    }

    /**
     * @param AssessmentEssayQuestion $question
     * @param array $data
     * @return AssessmentEssayQuestion|\Illuminate\Database\Eloquent\Model|null
     */
    public function update(AssessmentEssayQuestion $question, array $data): ?AssessmentEssayQuestion
    {
        $question->update($data);

        return $question->refresh();
    }
}
