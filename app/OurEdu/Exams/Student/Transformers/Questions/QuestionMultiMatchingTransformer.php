<?php


namespace App\OurEdu\Exams\Student\Transformers\Questions;


use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\ResourceSubjectFormats\Models\MultiMatching\MultiMatchingData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class QuestionMultiMatchingTransformer extends TransformerAbstract
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
     * @param MultiMatchingData $multiMatchingData
     * @return array
     */
    public function transform(MultiMatchingData $multiMatchingData)
    {


        $questions = [];
        $options = [];
        foreach ($multiMatchingData->questions as $question) {
            $questions[] = [
                'id' => $question->id,
                'text' => $question->text,
                'media'=> (object) questionMedia($question),
                'audio'=> (object) questionAudio($question),
                'video'=> (object) questionVideo($question),
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null,
            ];

        }

        foreach ($multiMatchingData->options as $option) {
            $optionsData = [
                'id' => $option->id,
                'option' => $option->option,
            ];

            if (isset($this->params['is_answer']) and !$this->params['is_exam']) {
                $optionsData['questions'] = $option->questions()->pluck('res_multi_matching_questions.id')->toArray();
            }

            $options[] = $optionsData;
        }
        $transformedData = [
            'id' => Str::uuid(),
            'description' => $multiMatchingData->description,
            'type' => OptionsTypes::MULTI_MATCHING_TYPE,
            'questions' => $questions,
            'options' => $options
        ];

        if (!isset($this->params['inside_practice'])) {
            $transformedData['time_to_solve'] = $multiMatchingData->time_to_solve;
        }

        if (isset($this->params['is_answer'])) {

            if (!$this->params['is_exam']) {
                $transformedData['question_feedback'] = (string)$multiMatchingData->question_feedback;
                $transformedData['is_correct_answer'] = (bool)$this->examQuestion->is_correct_answer;
            }

            $transformedData['is_answered'] = (bool)$this->examQuestion->is_answered;
            $transformedData['student_answer'] =  $this->studentAnswer();
            $transformedData["selected_options"] = $this->SelectedOptions();
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
                        'is_correct_answer' => (bool) $answer->is_correct_answer,
                        'question_id' => $answer->questionable->id,
                        'question' => $answer->questionable->text,
                        'media'=> (object) questionMedia($answer->questionable),
                        'option_id' => $answer->optionable->id,
                        'option' => $answer->optionable->option,
                    ];
                    $selectedAnswers[] = $answerData;
                }
            }
            return $selectedAnswers;
        }
    }

    private function selectedOptions()
    {
        $selectedAnswers = [];
        $answers = $this->student->answers()
            ->where('question_id', $this->examQuestion->id)
            ->get();
        if (!is_null($answers)) {
            foreach ($answers as $answer) {
                if ($answer->questionable && $answer->optionable) {
                    $answerData = [
                        'is_correct_answer' => (bool) $answer->is_correct_answer,
                        'question_id' => $answer->questionable->id,
                        'question' => $answer->questionable->text,
                        'media'=> (object) questionMedia($answer->questionable),
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

