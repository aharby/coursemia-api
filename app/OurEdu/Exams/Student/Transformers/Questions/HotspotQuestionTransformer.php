<?php


namespace App\OurEdu\Exams\Student\Transformers\Questions;

use App\OurEdu\Options\Enums\OptionsTypes;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class HotspotQuestionTransformer extends TransformerAbstract
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
     * @return array
     */
    public function transform($question)
    {
        $transformedData = [
            'id' => Str::uuid(),
            'question' => $question->question,
            'type' => OptionsTypes::HOTSPOT_TYPE,
            'image_width' => $question->image_width,
            'description' => $question->parentData->description,
            'media'=> (object) questionMedia($question),
            'audio'=> (object) questionAudio($question),
            'video'=> (object) questionVideo($question),
            'audio_link' => $question->audio_link ?? null,
            'video_link' => $question->video_link ?? null,
        ];
        if (!isset($this->params['inside_practice'])) {
            $transformedData['time_to_solve'] = $question->time_to_solve;

        }
//        $answers = [];

        if (isset($this->params['is_answer'])) {
            /*
             *  the business logic implies that the question must only have one answer so in this case
             *  we will take the first and in the fill resource side he will only add one answer
             */
//            foreach ($question->options as $answer) {
//                $answersData = [
//                    'id' => $answer->id,
//                    'answer' => $answer->answer,
//                ];
//
//                $answers[] = $answersData;
//            }


            if (!$this->params['is_exam']) {
                $transformedData['answer'] = $question->answer->answer ?? '';
                $transformedData['question_feedback'] = (string)$question->question_feedback;
                $transformedData['is_correct_answer'] = (bool)$this->examQuestion->is_correct_answer;
            }

            $transformedData['is_answered'] = (bool)$this->examQuestion->is_answered;
            $transformedData['student_answer'] = (object) $this->studentAnswer();
            $transformedData['selected_options'] = $this->SelectedOptions();
        }

//        if ($question->options()->count()) {
//            $answer = $question->options()->first();
//            $transformedData['accepted_answers'] = [
//                'id' => $answer->id,
//                'answer' => $answer->answer,
//            ];
//        }

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
