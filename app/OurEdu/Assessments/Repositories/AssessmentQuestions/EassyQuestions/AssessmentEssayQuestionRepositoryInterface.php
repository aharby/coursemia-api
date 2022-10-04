<?php


namespace App\OurEdu\Assessments\Repositories\AssessmentQuestions\EassyQuestions;


use App\OurEdu\Assessments\Models\Questions\Essay\AssessmentEssayQuestion;

interface AssessmentEssayQuestionRepositoryInterface
{
    /**
     * @param int $questionId
     * @return AssessmentEssayQuestion|null
     */
    public function findOrFail(int $questionId): ?AssessmentEssayQuestion;

    /**
     * @param array $data
     * @return AssessmentEssayQuestion
     */
    public function create(array $data): AssessmentEssayQuestion;

    /**
     * @param AssessmentEssayQuestion $question
     * @param array $data
     * @return AssessmentEssayQuestion|null
     */
    public function update(AssessmentEssayQuestion $question, array $data): ?AssessmentEssayQuestion;
}
