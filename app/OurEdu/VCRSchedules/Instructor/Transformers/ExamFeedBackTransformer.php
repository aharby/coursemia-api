<?php


namespace App\OurEdu\VCRSchedules\Instructor\Transformers;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class ExamFeedBackTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'
    ];
    protected array $availableIncludes = [
    ];

    public function __construct()
    {
    }

    public function transform($exam)
    {
        $this->questionFeedback($exam);
        $total = $exam->questions()->count();
        $correctAnswers = $exam->questions()->where('is_correct_answer', 1)->count();

        $percent =$total>0? $correctAnswers / $total * 100:0;

        $transformerData = [
            'id' => Str::uuid(),
            'result' => "{$correctAnswers} / {$total}",
            'result_percent' => $percent . '%',

        ];
        return $transformerData;
    }

    function questionFeedback($exam)
    {
        $questions = $exam->questions;
        $questionFeedback = [];
        foreach ($questions as $question) {

            $isCorrectAnswer = $question->is_correct_answer == 0 ? false : true;

            $mainQuestion = $question->questionable()->first();

            $return = $this->questionData($mainQuestion, $question->slug, $isCorrectAnswer);

            $questionFeedback[] = $return;
        }
        return $questionFeedback;
    }

    function questionData($mainQuestion, $slug, $isCorrectAnswer)
    {
        $text = '';
        $answers = [];
        switch ($slug) {
            case (LearningResourcesEnums::TRUE_FALSE):
                $text = $mainQuestion->text;
                if ($isCorrectAnswer == false) {
                    $answers = $mainQuestion->options()->where('is_correct_answer', 1)->pluck('option')->toArray();
                    $isTrue = $mainQuestion->is_true == 1 ? true : false;
                    $answers[] = [$isTrue];
                }

                break;
            case (LearningResourcesEnums::MULTI_CHOICE):
                $text = $mainQuestion->question;
                if ($isCorrectAnswer == false) {
                    $answers = $mainQuestion->options()->where('is_correct_answer', 1)->pluck('answer')->toArray();
                }
                break;
            case (LearningResourcesEnums::DRAG_DROP):
                $text = $mainQuestion->description;

                break;
            case (LearningResourcesEnums::MATCHING):
                $text = $mainQuestion->description;

                break;
            case (LearningResourcesEnums::MULTIPLE_MATCHING):
                $text = $mainQuestion->description;

                break;
        }
        $questionData = [
            'is_correct' => $isCorrectAnswer,
            'text' => $text,
        ];
        if ($isCorrectAnswer == false) {
            $questionData['correct_answers'] = $answers;
        }

        return $questionData;
    }

    public function includeActions() {


    }
}
