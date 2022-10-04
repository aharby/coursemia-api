<?php


namespace App\OurEdu\Exams\Student\Transformers\Competitions\Questions;


use App\OurEdu\Exams\Models\Competitions\CompetitionStudent;
use App\OurEdu\Exams\Models\ExamQuestionAnswer;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceOption;
use League\Fractal\TransformerAbstract;

class CompetitionQuestionMultipleChoiceTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
    ];
    protected array $availableIncludes = [
    ];

    private $params;
    private $student;
    private $examQuestion;
    private  $answers;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->student = $this->params['student'];
        $this->examQuestion = $this->params['examQuestion'];
    }

    /**
     * @param MultipleChoiceData $multipleChoiceData
     * @return array
     */
    public function transform($question)
    {
        $questions = [];

        if ( isset($this->params['exam_id']) && (isset($this->params['is_answer'])) ) {
            $count = CompetitionStudent::where('exam_id',$this->params['exam_id'])
                ->count();
        }

        $options = [];
        foreach ($question->options as $option) {
            $optionsData = [
                'id' => $option->id,
                'option' => $option->answer,
            ];

            if (isset($this->params['is_answer'])) {
                $optionsData['is_correct_answer'] = (bool)$option->is_correct_answer;

                    $singleOptionsCount = ExamQuestionAnswer::where('option_table_type', MultipleChoiceOption::class)
                        ->where('option_table_id', $option->id)->count();

                    $optionData['percent'] = round(getNumberOfPercent($singleOptionsCount, $count));

            }
            $options[] = $optionsData;
        }
        $questions = [
            'id' => $question->id,
            'question' => $question->question,
            'url' => $question->url,
            'type' => $question->parentData->multipleChoiceType ? $question->parentData->multipleChoiceType->slug : null,
            'question_type' => $question->parentData->multipleChoiceType ? $question->parentData->multipleChoiceType->slug : null,
            'time_to_solve' => $question->time_to_solve ??  env('TIME_TO_SOLVE_QUESTION',30),
            'description' => $question->parentData->description,
            'media'=> (object) questionMedia($question),
            'options' => $options,
        ];
        $questions['question_feedback'] = (string) $question->question_feedback;
        $questions['is_answered'] = false;
        $questions['is_correct_answer'] = false;
        $questions['selected_options'] =  $this->SelectedOptions($question);
        $questions['student_answer'] =  $this->studentAnswer($question);

        if (isset($this->params['is_answered']) && (bool)$this->params['is_answered']) {
            $questions['is_answered'] = (bool) $this->params['is_answered'];
            $questions['is_correct_answer']  = isset($this->params['answers']) ? (bool)$this->params['answers']->is_correct_answer : false;
        }
        return $questions;
    }

    private function studentAnswer($question)
    {
        $returnedData = [];
        // multiple choice case
        if ($question->parentData->multipleChoiceType?->slug == ResourceOptionsSlugEnum::MULTIPLE_CHOICE_SLUG_MULTIPLE_CHOICE) {
            $answers = $this->student->answers()
                ->where('question_id', $this->examQuestion->id)
                ->get();
            if (!is_null($answers)) {
                $selectedOptions = [];
                foreach ($answers as $answer) {
                    if ($answer->optionable) {
                        $optionsData = [
                            'answer_id' => $answer->optionable->id,
                            'answer' => $answer->optionable->answer
                        ];
                        $selectedOptions[] = $optionsData;
                    }
                }
                return $selectedOptions;
            }
        }else {
            // single choice case
            $answer = $this->student->answers()
                ->where('question_id', $this->examQuestion->id)
                ->first();
            if (!is_null($answer)) {
                $returnedData []= [
                    'answer_id' => $answer->optionable->id,
                    'answer' => $answer->optionable->answer
                ];
                return $returnedData;
            }
        }

        return $returnedData;
    }

    private function SelectedOptions($question)
    {
        $selectedOptions = [];
        // multiple choice case
        if ($question->parentData->multipleChoiceType?->slug == ResourceOptionsSlugEnum::MULTIPLE_CHOICE_SLUG_MULTIPLE_CHOICE) {
            $answers = $this->student->answers()
                ->where('question_id', $this->examQuestion->id)
                ->get();
            if (!is_null($answers)) {
                $selectedOptions = [];
                foreach ($answers as $answer) {
                    if ($answer->optionable) {
                        $optionsData = [
                            'answer_id' => $answer->optionable->id,
                            'answer' => $answer->optionable->answer
                        ];
                        $selectedOptions[] = $optionsData;
                    }
                }
            }
        }else {
            // single choice case
            $answer = $this->student->answers()
                ->where('question_id', $this->examQuestion->id)
                ->first();
            if (!is_null($answer)) {
                $returnedData = [
                    'answer_id' => $answer->optionable->id,
                    'answer' => $answer->optionable->answer
                ];
                $selectedOptions[] = $returnedData;
            }
        }

        return $selectedOptions;
    }

}

