<?php


namespace App\OurEdu\Exams\Student\Transformers\Questions;

use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class QuestionTrueFalseTransformer extends TransformerAbstract
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
     * @param TrueFalseData $trueFalseData
     * @return array
     */
    public function transform($question)
    {
        $options = [];
        foreach ($question->options as $option) {
            $optionData =
                [
                    'id' => $option->id,
                    'option' => $option->option,
                ];

            if (isset($this->params['is_answer']) and !$this->params["is_exam"]) {
                $optionData['is_correct'] = (bool)$option->is_correct_answer;
            }

            $options[] = $optionData;
        }

        $transformedData = [
            'id' => Str::uuid(),
            'type' => $question->parentData->TrueFalseType ? $question->parentData->TrueFalseType->slug : null,
            'question_type' => $question->parentData->TrueFalseType ? $question->parentData->TrueFalseType->slug : null,
            'text' => $question->text,
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

        if (count($options)) {
            $transformedData['options'] = $options;
        }

        if (isset($this->params['is_answer'])) {
            if (!$this->params['is_exam']) {
                $transformedData['is_true'] = (bool)$question->is_true;
                $transformedData['question_feedback'] = (string)$question->question_feedback;
                $transformedData['is_correct_answer'] = (bool)$this->examQuestion->is_correct_answer;
            }

            $transformedData['is_answered'] = (bool)$this->examQuestion->is_answered;
            $transformedData['student_answer'] = (object) $this->studentAnswer($question);
            $transformedData["selected_options"] = $this->SelectedOptions($question);
        }

        return $transformedData;
    }

    private function studentAnswer($question)
    {
        $answer = $this->student->answers()
                    ->where('question_id', $this->examQuestion->id)
                    ->first();

        if (!is_null($answer)) {
            $returnedData['answer_text'] = !is_bool($answer->answer_text)?($answer->answer_text == 'true' || $answer->answer_text == '1' ? true : false):$answer->answer_text;
            $returnedData['answer_text_true_false'] = !is_bool($answer->answer_text)?($answer->answer_text == 'true' || $answer->answer_text == '1' ? true : false):$answer->answer_text;
            if ($question->parentData->TrueFalseType->slug == ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT){
                if ($answer->optionable) {
                    $returnedData['option_id'] = $answer->optionable->id;
                    $returnedData['option'] = $answer->optionable->option;
                }
            }
            return $returnedData;
        }
    }


    private function SelectedOptions($question)
    {
        $selectedOptions = [];
        $answer = $this->student->answers()
                    ->where('question_id', $this->examQuestion->id)
                    ->first();

        if (!is_null($answer)) {
            $returnedData['answer_text'] = !is_bool($answer->answer_text)?($answer->answer_text == 'true' || $answer->answer_text == '1' ? true : false):$answer->answer_text;
            if ($question->parentData->TrueFalseType->slug == ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT){
                if ($answer->optionable) {
                    $returnedData['answer_id'] = $answer->optionable->id;
                    $returnedData['answer'] = $answer->optionable->option;
                }
            }
            $selectedOptions[] = $returnedData;
        }

        return $selectedOptions;
    }
}
