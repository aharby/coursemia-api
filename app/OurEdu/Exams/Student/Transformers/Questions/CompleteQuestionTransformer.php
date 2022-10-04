<?php


namespace App\OurEdu\Exams\Student\Transformers\Questions;

use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\ResourceSubjectFormats\Models\Complete\CompleteData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class CompleteQuestionTransformer extends TransformerAbstract
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
        $this->params['is_exam'] = $this->params['is_exam'] ?? false;
    }

    /**
     * @param CompleteData $multipleChoiceData
     * @return array
     */
    public function transform($question)
    {
        $transformedData = [
            'id' => Str::uuid(),
            'type' => OptionsTypes::COMPLETE_TYPE,
            'question' => $question->question,
            'description' => $question->parentData->description,
        ];
        if (!isset($this->params['inside_practice'])) {
            $transformedData['time_to_solve'] = $question->time_to_solve;

        }
        $answers = [];

        if (isset($this->params['is_answered'])) {

            if (!$this->params['is_exam']) {
                foreach ($question->options as $answer) {
                    $answersData = [
                        'id' => $answer->id,
                        'answer' => trim(strip_tags($answer->answer)),
                    ];

                    $answers[] = $answersData;
                }


                $transformedData['answer'] = trim(strip_tags($question->answer->answer));
                $transformedData['question_feedback'] = (string)$question->question_feedback;
                $transformedData['is_correct_answer'] = (bool)$this->examQuestion->is_correct_answer;
            }

            $transformedData['is_answered'] = (bool)$this->examQuestion->is_answered;
            $transformedData['student_answer'] = (object) $this->studentAnswer();
            $transformedData['selected_options'] = $this->SelectedOptions();
        }

        if (count($answers)) {
            $transformedData['accepted_answers'] = $answers;
        }

        return $transformedData;
    }


    private function studentAnswer()
    {
        $answer = $this->student->answers()
            ->where('question_id', $this->examQuestion->id)
            ->first();

        if (!is_null($answer)) {
            $returnedData = [
                'answer_text' => (string) $answer->answer_text,
                'answer_text_complete' => (string) $answer->answer_text,
            ];
            return $returnedData;
        }
    }

    private function SelectedOptions()
    {
        $selectedAnswers = [];
        $answer = $this->student->answers()
            ->where('question_id', $this->examQuestion->id)
            ->first();

        if (!is_null($answer)) {
            $returnedData = [
                'answer_text' => (string) $answer->answer_text
            ];

            $selectedAnswers[] = $returnedData;
        }

        return $selectedAnswers;
    }
}
