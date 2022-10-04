<?php


namespace App\OurEdu\Exams\Student\Transformers\Questions;


use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class QuestionMultipleChoiceTransformer extends TransformerAbstract
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
     * @param MultipleChoiceData $multipleChoiceData
     * @return array
     */
    public function transform($question)
    {
        $questions = [];

        $options = [];
        foreach ($question->options as $option) {
            $optionsData = [
                'id' => $option->id,
                'option' => $option->answer,
            ];

            if (isset($this->params['is_answer']) and !$this->params['is_exam']) {
                $optionsData['is_correct_answer'] = (bool)$option->is_correct_answer;
            }
            $options[] = $optionsData;
        }

        $questions = [
            'id' => Str::uuid(),
            'type' => $question->parentData->multipleChoiceType ? $question->parentData->multipleChoiceType->slug : null,
            'question_type' => $question->parentData->multipleChoiceType ? $question->parentData->multipleChoiceType->slug : null,
            'question' => $question->question,
            'url' => $question->url,
            'media' => (object) questionMedia($question),
            'audio'=> (object) questionAudio($question),
            'video'=> (object) questionVideo($question),
            'audio_link' => $question->audio_link ?? null,
            'video_link' => $question->video_link ?? null,
            'description' => $question->parentData->description,
            'options' => $options,
        ];

        if (!isset($this->params['inside_practice'])) {
            $questions['time_to_solve'] = $question->time_to_solve;
        }

        if (isset($this->params['is_answer'])) {
            if (!$this->params['is_exam']) {
                $questions['question_feedback'] = (string)$question->question_feedback;
                $questions['is_correct_answer'] = (bool)$this->examQuestion->is_correct_answer;
            }

            $questions['is_answered'] = (bool)$this->examQuestion->is_answered;
            $questions["student_answer"] =  $this->studentAnswer($question);
            $questions["selected_options"] =  $this->SelectedOptions($question);
        }

        return $questions;
    }

    private function studentAnswer($question)
    {
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
            
            if (!is_null($answer) && $answer->optionable) {
                $returnedData[] = [
                    'answer_id' => $answer->optionable->id,
                    'answer' => $answer->optionable->answer
                ];
               
                return $returnedData;
            }
        }

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
            if (!is_null($answer) && $answer->optionable) {
                $selectedOptions[] = [
                    'answer_id' => $answer->optionable->id,
                    'answer' => $answer->optionable->answer
                ];
            }
        }

        return $selectedOptions;

    }
}

