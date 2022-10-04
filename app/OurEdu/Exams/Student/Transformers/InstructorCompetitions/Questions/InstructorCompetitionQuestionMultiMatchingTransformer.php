<?php


namespace App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\Questions;


use App\OurEdu\Exams\Models\ExamQuestionAnswer;
use App\OurEdu\Exams\Models\InstructorCompetitions\InstructorCompetitionStudent;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropOption;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingOption;
use League\Fractal\TransformerAbstract;

class InstructorCompetitionQuestionMultiMatchingTransformer extends TransformerAbstract
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
     * @param MultiMatchingData $multiMatchingData
     * @return array
     */
    public function transform(MultiMatchingData $multiMatchingData)
    {

        if (isset($this->params['exam_id']) && (isset($this->params['is_answer']))) {
            $count = InstructorCompetitionStudent::where('exam_id',$this->params['exam_id'])
                ->count();
        }
        $questions = [];
        $options = [];
        foreach ($multiMatchingData->questions as $question) {
            $questions[] = [
                'id' => $question->id,
                'text' => $question->text,
                'media'=> (object) questionMedia($question),
            ];

        }

        foreach ($multiMatchingData->options as $option) {
            $optionsData = [
                'id' => $option->id,
                'option' => $option->option
            ];

            if (isset($this->params['is_answer'])) {
                $optionsData['questions'] = $option->questions()->pluck('res_multi_matching_questions.id')->toArray();
            }
            if (isset($this->params['is_answer']) && (isset($this->params['exam_id']))) {

                $singleOptionsCount = ExamQuestionAnswer::where('option_table_type', MultiMatchingOption::class)
                    ->where('option_table_id', $option->id)->count();

                $optionData['percent'] = round(getNumberOfPercent($singleOptionsCount, $count));
            }
            $options[] = $optionsData;
        }
        $transformedData = [
            'id' => $multiMatchingData->id,
            'description' => $multiMatchingData->description,
            'time_to_solve' => (int) $multiMatchingData->time_to_solve ?? env('TIME_TO_SOLVE_QUESTION',30),
            'questions' => $questions,
            'options' => $options

        ];
        $transformedData['question_feedback'] = (string) $question->question_feedback;
        $transformedData['student_answer'] = (object) $this->studentAnswer();
        $transformedData['is_answered'] = false;
        $transformedData['is_correct_answer'] = false;

        if (isset($this->params['is_answered']) && (bool)$this->params['is_answered']) {
            $transformedData['is_answered'] = (bool) $this->params['is_answered'];
            $transformedData['is_correct_answer']  = isset($this->params['answers']) ? (bool)$this->params['answers']->is_correct_answer : false;
        }
        return $transformedData;
    }

    private function studentAnswer()
    {
        $answers = $this->student->answers()
            ->where('question_id', $this->examQuestion->id)
            ->get();
        if (!is_null($answers)) {
            $selectedAnswers = [];
            foreach ($answers as $answer) {
                $answerData = [
                    'is_correct_answer' => (bool) $answer->is_correct_answer,
                    'question_id' => $answer->questionable->id,
                    'question' => $answer->questionable->text,
                    'media'=> (object) questionMedia($answer->questionable),
                    'option_id' => $answer->optionable->id,
                    'option' => $answer->optionable->option
                ];
                $selectedAnswers[] = $answerData;
            }
            return $selectedAnswers;
        }
    }
}

