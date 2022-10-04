<?php


namespace App\OurEdu\Exams\Student\Transformers\Questions;

use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class QuestionDragDropTransformer extends TransformerAbstract
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
     * @param DragDropData $dragDropData
     * @return array
     */
    public function transform(DragDropData $dragDropData)
    {

        $questions = [];
        foreach ($dragDropData->questions as $question) {

            $questionsData = [
                'id' => $question->id,
                'question' => $question->question,
                'media'=> (object) questionMedia($question),
                'audio'=> (object) questionAudio($question),
                'video'=> (object) questionVideo($question),
                'audio_link' => $question->audio_link ?? null,
                'video_link' => $question->video_link ?? null
            ];
            if (isset($this->params['is_answer']) and !$this->params['is_exam']) {
                $questionsData['answers'] = $question->correct_option_id;
            }
            $questions[] = $questionsData;

        }
        $options = [];
        foreach ($dragDropData->options as $option) {


            $options[] = [
                'id' => $option->id,
                'option' => $option->option,
            ];

        }

        $returnedData = [
            'id' => Str::uuid(),
            'type' => OptionsTypes::DRAG_DROP_DRAG_DROP_TYPE,
            'description' => $dragDropData->description,
            'questions' => $questions,
            'options' => $options
        ];
        if (!isset($this->params['inside_practice'])) {
            $returnedData['time_to_solve'] = $dragDropData->time_to_solve;
        }

        if (isset($this->params['is_answer'])) {
            if (!$this->params['is_exam']) {
                $returnedData['question_feedback'] = (string)$dragDropData->question_feedback;
                $returnedData['is_correct_answer'] = (bool)$this->examQuestion->is_correct_answer;
            }

            $returnedData['is_answered'] = (bool)$this->examQuestion->is_answered;
            $returnedData['student_answer'] =  $this->studentAnswer();
            $returnedData['selected_options'] =  $this->SelectedOptions();
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
                        'question_id' => $answer->questionable->id,
                        'is_correct_answer' => (bool)$answer->is_correct_answer,
                        'question' => $answer->questionable->question,
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
                        'is_correct_answer' => (bool)$answer->is_correct_answer,
                        'question' => $answer->questionable->question,
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

