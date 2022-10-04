<?php


namespace App\OurEdu\Assessments\Repositories\AssessmentQuestions;

use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\AssessmentAnswer;
use App\OurEdu\Assessments\Models\AssessmentQuestion;

class AssessmentQuestionRepository implements AssessmentQuestionRepositoryInterface
{
    public function create(array $data)
    {
        return AssessmentQuestion::query()->create($data);
    }


    public function updateOrCreate(int $assessmentId,int $questionId,array $data){
        return AssessmentQuestion::query()->updateOrCreate([
            'assessment_id'=>$assessmentId,
            'question_id'=>$questionId,
            'question_type'=>$data['question_type']
        ],$data);
    }


    public function findAssessmentQuestion(Assessment $assessment, int $questionId)
    {
        return $assessment->questions()->with('assessorsAnswers.details')->where('id', $questionId)->firstOrFail();
    }


    public function findOrFail($questionId): ?AssessmentQuestion
    {
        return AssessmentQuestion::findOrFail($questionId);
    }

    /**
     * Delete Question and its relation
     * @param App\OurEdu\Assessments\Models\Assessment $assessment
     * @param App\OurEdu\Assessments\Models\AssessmentQuestion $assessmentQuestion
     * @return bool
     */
    public function delete(Assessment $assessment, AssessmentQuestion $assessmentQuestion)
    {
        return $assessment->questions()->where('id', $assessmentQuestion->id)->delete() &&
            $assessmentQuestion->question()->delete();
    }



    public function updateOrCreateAnswer($question, $data)
    {
        if (
            $answer = $question->assessorsAnswers
                // ->with('details')
                ->where('assessment_user_id', $data['assessment_user_id'])
                ->first()
        ) {
            $answer->update($data);
        } else {
            $answer = AssessmentAnswer::create($data);
        }

        if (isset($data['details']) && count($data['details'])>0) {
            $answer->details()->delete();
            foreach ($data['details'] as $detail) {
                $answer->details()->create($detail);
            }
        }else{
            $answer->details()->delete();
        }
        return $answer;
    }
}
