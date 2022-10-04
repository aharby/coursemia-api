<?php


namespace App\OurEdu\Exams\Student\Transformers\Competitions\Questions;


use App\OurEdu\Exams\Models\Competitions\CompetitionStudent;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class CompetitionQuestionHotspotTransformer extends TransformerAbstract
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
     * @return array
     */
    public function transform( $question)
    {
        $transformedData = [
            'id' => Str::uuid(),
            'question' => $question->question,
            'type' => OptionsTypes::HOTSPOT_TYPE,
            'image_width' => $question->image_width,
            'time_to_solve' => $question->time_to_solve ??  env('TIME_TO_SOLVE_QUESTION',30),
            'description' => $question->parentData->description,
            'media'=> (object) questionMedia($question)
        ];
        $transformedData['is_answered'] = false;
        $transformedData['is_correct_answer'] = false;
        $transformedData['answer'] = $question->answer->answer ?? '';
        $transformedData['question_feedback'] = (string) $question->question_feedback;
        $transformedData['student_answer'] = (object) $this->studentAnswer();
        $transformedData['selected_options'] = $this->SelectedOptions();

        if (isset($this->params['is_answered']) && (bool)$this->params['is_answered']) {
            $transformedData['is_answered'] = (bool)$this->params['is_answered'];
            $transformedData['is_correct_answer']  = isset($this->params['answers']) ? (bool)$this->params['answers']->is_correct_answer : false;
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
                'answer_text' => (string) $answer->answer_text
            ];
            return $returnedData;
        }
    }

    private function SelectedOptions()
    {
        $selectedOptions = [];
        $answer = $this->student->answers()
            ->where('question_id', $this->examQuestion->id)
            ->first();

        if (!is_null($answer)) {
            $returnedData = [
                'answer_text' => (string) $answer->answer_text
            ];
            $selectedOptions[] = $returnedData;
        }

        return $selectedOptions;
    }

}

