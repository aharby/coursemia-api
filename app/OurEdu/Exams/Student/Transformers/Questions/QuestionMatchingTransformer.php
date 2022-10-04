<?php


namespace App\OurEdu\Exams\Student\Transformers\Questions;


use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\ResourceSubjectFormats\Models\Matching\MatchingData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class QuestionMatchingTransformer extends TransformerAbstract
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
     * @param MatchingData $matchingData
     * @return array
     */
    public function transform(MatchingData $matchingData)
    {
        $questions = [];
        $options = [];
        foreach ($matchingData->questions as $question) {
            $questions[] = [
                'id' => $question->id,
                'text' => $question->text,
                'media' => (object)questionMedia($question),
                'audio' => (object)questionAudio($question),
                'video' => (object)questionVideo($question),
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
            ];
        }
        foreach ($matchingData->options as $option) {

            $optionsData = [
                'id' => $option->id,
                'option' => $option->option,
            ];

            if (isset($this->params['is_answer']) and !$this->params['is_exam']) {
                $optionsData['question_id'] = $option->res_matching_question_id;
            }
            $options[] = $optionsData;
        }

        $transformedData = [
            'id' => Str::uuid(),
            'type' => OptionsTypes::MATCHING_TYPE,
            'description' => $matchingData->description,
            'questions' => $questions,
            'options' => $options
        ];

        if (!isset($this->params['inside_practice'])) {
            $transformedData['time_to_solve'] = $matchingData->time_to_solve;
        }

        if (isset($this->params['is_answer'])) {
            if (!$this->params['is_exam']) {
                $transformedData['question_feedback'] = (string)$matchingData->question_feedback;
                $transformedData['is_correct_answer'] = (bool)$this->examQuestion->is_correct_answer;
            }

            $transformedData['is_answered'] = (bool)$this->examQuestion->is_answered;
            $transformedData['student_answer'] = $this->studentAnswer();
            $transformedData['selected_options'] = $this->SelectedOptions();
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
                if ($answer->questionable && $answer->optionable) {
                    $answerData = [
                        'question_id' => $answer->questionable->id,
                        'question' => $answer->questionable->text,
                        'media' => (object)questionMedia($answer->questionable),
                        'option_id' => $answer->optionable->id,
                        'option' => $answer->optionable->option
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
                        'question_id' => $answer->questionable->id,
                        'question' => $answer->questionable->text,
                        'media' => (object)questionMedia($answer->questionable),
                        'answer_id' => $answer->optionable->id,
                        'answer' => $answer->optionable->option
                    ];
                    $selectedAnswers[] = $answerData;
                }
            }
        }

        return $selectedAnswers;
    }
}

