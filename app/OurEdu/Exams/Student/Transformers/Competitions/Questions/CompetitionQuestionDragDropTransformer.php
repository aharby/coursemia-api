<?php


namespace App\OurEdu\Exams\Student\Transformers\Competitions\Questions;


use App\OurEdu\Exams\Models\Competitions\CompetitionStudent;
use App\OurEdu\Exams\Models\ExamQuestionAnswer;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropOption;
use League\Fractal\TransformerAbstract;

class CompetitionQuestionDragDropTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [


    ];
    protected array $availableIncludes = [
    ];

    private $params;
    private $student;
    private $examQuestion;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->student = $this->params['student'];
        $this->examQuestion = $this->params['examQuestion'];
    }

    /**
     * @param DragDropData $dragDropData
     * @return array
     */
    public function transform(DragDropData $dragDropData)
    {

        if (isset($this->params['exam_id']) && (isset($this->params['is_answer']))) {
            $count = CompetitionStudent::where('exam_id',$this->params['exam_id'])
                ->count();
        }

        $questions = [];
        foreach ($dragDropData->questions as $question) {

            $questionsData = [
                'id' => $question->id,
                'question' => $question->question,
                'media'=> (object) questionMedia($question),
            ];
            if (isset($this->params['is_answer']) && (bool)$this->params['is_answer']) {
                $questionsData['answers'] = $question->correct_option_id;

            }
            $questions[] = $questionsData;

        }
        $options = [];
        foreach ($dragDropData->options as $option) {


            $optionData = [
                'id' => $option->id,
                'option' => $option->option,
            ];

            if (isset($this->params['is_answer']) && (bool)$this->params['is_answer'] && (isset($this->params['exam_id']))) {

                $singleOptionsCount = ExamQuestionAnswer::where('option_table_type', DragDropOption::class)
                    ->where('option_table_id', $option->id)->count();

                $optionData['percent'] = round(getNumberOfPercent($singleOptionsCount, $count));
            }
            $options[] = $optionData;
        }
        $returnedData = [
            'id' => $dragDropData->id,
            'description' => $dragDropData->description,
            'type' => OptionsTypes::DRAG_DROP_DRAG_DROP_TYPE,
            'time_to_solve' => (int) $dragDropData->time_to_solve ??  env('TIME_TO_SOLVE_QUESTION',30),
            'questions' => $questions,
            'options' => $options

        ];
        $returnedData['question_feedback'] = (string) $dragDropData->question_feedback;
        $returnedData['student_answer'] =  $this->studentAnswer();
        $returnedData['selected_options'] =  $this->SelectedOptions();
        $returnedData['is_answered'] = false;
        $returnedData['is_correct_answer'] = false;

        if (isset($this->params['is_answered']) && (bool)$this->params['is_answered']) {
            $returnedData['is_answered'] = (bool)$this->params['is_answered'];
            $returnedData['is_correct_answer']  = isset($this->params['answers']) ? (bool)$this->params['answers']->is_correct_answer : false;
        }

        return $returnedData;
    }

    private function studentAnswer()
    {
        $answers = $this->student->answers()
            ->where('question_id', $this->examQuestion->id)
            ->get();
        if (!is_null($answers)) {
            $selectedAnswers = [];
            foreach ($answers as $answer) {
                if ($answer->questionable && $answer->optionable) {
                    $answerData = [
                        'is_correct_answer' => (bool)$answer->is_correct_answer,
                        'question_id' => $answer->questionable->id,
                        'question' => $answer->questionable->question,
                        'media' => (object)questionMedia($answer->questionable),
                        'option_id' => $answer->optionable->id,
                        'option' => $answer->optionable->option,
                    ];
                    $selectedAnswers[] = $answerData;
                }
            }
            return $selectedAnswers;
        }
    }

    private function SelectedOptions()
    {
        $selectedAnswers = [];
        $answers = $this->student->answers()
            ->where('question_id', $this->examQuestion->id)
            ->get();
        if (!is_null($answers)) {
            foreach ($answers as $answer) {
                if ($answer->questionable && $answer->optionable) {
                    $answerData = [
                        'is_correct_answer' => (bool)$answer->is_correct_answer,
                        'question_id' => $answer->questionable->id,
                        'question' => $answer->questionable->question,
                        'media' => (object)questionMedia($answer->questionable),
                        'answer_id' => $answer->optionable->id,
                        'answer' => $answer->optionable->option,
                    ];
                    $selectedAnswers[] = $answerData;
                }
            }
        }

        return $selectedAnswers;
    }

}

