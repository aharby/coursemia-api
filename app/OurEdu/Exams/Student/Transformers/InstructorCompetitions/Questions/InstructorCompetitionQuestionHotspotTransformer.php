<?php

namespace App\OurEdu\Exams\Student\Transformers\InstructorCompetitions\Questions;


use App\OurEdu\Exams\Models\Competitions\CompetitionStudent;
use App\OurEdu\Exams\Models\ExamQuestionAnswer;
use App\OurEdu\Exams\Models\InstructorCompetitions\InstructorCompetitionStudent;
use League\Fractal\TransformerAbstract;

class InstructorCompetitionQuestionHotspotTransformer extends TransformerAbstract
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
     * @param HotSpotData $multipleChoiceData
     * @return array
     */
    public function transform($question)
    {
        $transformedData = [];

        if (isset($this->params['exam_id']) && (isset($this->params['is_answer']))) {
            $count = InstructorCompetitionStudent::where('exam_id',$this->params['exam_id'])
                ->count();
        }

        $transformedData = [
            'id' => $question->id,
            'question' => $question->question,
        ];

        $answers = [];

        if (isset($this->params['is_answer']) && (bool)$this->params['is_answer']) {
            foreach ($question->options as $answer) {
                $answersData = [
                    'id' => $answer->id,
                    'answer' => $answer->answer,
                ];

                // todo : answer to be calculated automatically
                $singleOptionsCount = ExamQuestionAnswer::where('option_table_type', HotSpotAnswer::class)
                    ->where('option_table_id', $answer->id)->count();

                $answersData['percent'] = round(getNumberOfPercent($singleOptionsCount, $count));
            }

            $answers[] = $answersData;
        }
        $transformedData['answer'] = $question->answer->answer ?? '';
        $transformedData['question_feedback'] = (string) $question->question_feedback;
        $transformedData['student_answer'] = (object) $this->studentAnswer();
        $transformedData['is_answered'] = false;
        $transformedData['is_correct_answer'] = false;

        if(isset($this->params['is_answered']) && ((bool)$this->params['is_answered'])){
            $transformedData['is_answered'] = (bool) $this->params['is_answered'];
            $transformedData['is_correct_answer']  = isset($this->params['answers']) ? (bool)$this->params['answers']->is_correct_answer : false;
        }

        $transformedData['accepted_answers'] = $answers;

        return $transformedData;
    }

    private function studentAnswer()
    {
        $answer = $this->student->answers()
            ->where('question_id', $this->examQuestion->id)
            ->first();

        if (!is_null($answer)) {
            $returnedData = [
                'answer_text' => (string) $answer->answer_text
            ];
            return $returnedData;
        }
    }
}

