<?php


namespace App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\Questions;


use App\OurEdu\Exams\Models\Competitions\CompetitionStudent;
use App\OurEdu\Exams\Models\ExamQuestionAnswer;
use App\OurEdu\Exams\Models\InstructorCompetitions\InstructorCompetitionStudent;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseOption;
use League\Fractal\TransformerAbstract;

class InstructorCompetitionQuestionTrueFalseTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
    ];

    private $params;
    private $student;
    private $examQuestion;
    private $answers;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->student = $this->params['student'];
        $this->examQuestion = $this->params['examQuestion'];
    }


    /**
     * @param TrueFalseData $trueFalseData
     * @return array
     */
    public function transform($question)
    {

        $this->answers = $this->student->answers()
                ->where('question_id', $this->examQuestion->id)
                ->first();

        if (isset($this->params['exam_id']) && (isset($this->params['is_answer']))) {
            $count = InstructorCompetitionStudent::where('exam_id',$this->params['exam_id'])
                ->count();
        }

        $options = [];
        foreach ($question->options as $option) {


            $optionData =
                [
                    'id' => $option->id,
                    'option' => $option->option,
                ];
            if (isset($this->params['is_answer'])) {

                $singleOptionsCount = ExamQuestionAnswer::where('option_table_type', TrueFalseOption::class)
                    ->where('option_table_id', $option->id)->count();

                $optionData['is_correct'] = (bool)$option->is_correct_answer;
                $optionData['percent'] = round(getNumberOfPercent($singleOptionsCount, $count));
            }

            $options[] = $optionData;
        }
        $questions = [
            'id' => $question->id,
            'text' => $question->text,
            'question_type' => $question->parentData->TrueFalseType ? $question->parentData->TrueFalseType->slug : null,

            'time_to_solve' => $question->time_to_solve ?? env('TIME_TO_SOLVE_QUESTION',30),
            'description' => $question->parentData->description,
            'media'=> (object) questionMedia($question),
            'options' => $options,
        ];
        $questions['question_feedback'] = (string) $question->question_feedback;
        $questions['student_answer'] = (object) $this->studentAnswer($question);
        $questions['is_true'] = (bool)$question->is_true;
        $questions['is_answered'] = false;
        $questions['is_correct_answer'] = false;

        if (isset($this->params['is_answered']) && (bool)$this->params['is_answered']) {
            $questions['is_answered'] = (bool) $this->params['is_answered'];
            $questions['is_correct_answer']  = isset($this->params['answers']) ? (bool)$this->params['answers']->is_correct_answer : false;
        }

        return $questions;
    }

    private function studentAnswer($question)
    {
        $answer = $this->student->answers()
            ->where('question_id', $this->examQuestion->id)
            ->first();

        if (!is_null($answer)) {
            $returnedData['answer_text'] = (string) $answer->answer_text;
            if ($question->parentData->TrueFalseType->slug == ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT){
                $returnedData['option_id'] = $answer->optionable->id;
                $returnedData['option'] = $answer->optionable->option;
            }
            return $returnedData;
        }
    }

}

