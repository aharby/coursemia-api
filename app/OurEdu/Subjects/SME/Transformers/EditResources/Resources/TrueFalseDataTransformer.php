<?php


namespace App\OurEdu\Subjects\SME\Transformers\EditResources\Resources;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Transformers\LearningResourceAcceptCriteriaGetTransformer;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Enums\ResourceOptionsSlugEnum;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\ResourceSubjectFormats\Models\TrueFalse\TrueFalseData;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class TrueFalseDataTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [


    ];
    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    /**
     * @param TrueFalseData $trueFalseData
     * @return array
     */
    public function transform(TrueFalseData $trueFalseData)
    {
        $questions = [];
        foreach ($trueFalseData->questions as $question) {
            $options = [];
            foreach ($question->options as $option) {
                $options[] = [
                    'id' => $option->id,
                    'option' => $option->option,
                    'is_correct' => (bool)$option->is_correct_answer
                ];
            }

            if ($trueFalseData->TrueFalseType->slug == ResourceOptionsSlugEnum::TRUE_FALSE_WITH_CORRECT) {
                $questions[] = [
                    'id' => $question->id,
                    'text' => $question->text,
                    'media' => (object)questionMedia($question),
                    'audio' => (object)questionAudio($question),
                    'video' => (object)questionVideo($question),
                    'question_feedback' => $question->question_feedback,
                    'is_true' => (bool)$question->is_true,
                    'options' => $options,
                    'audio_link' => $question->audio_link ?? null,
                    'video_link' => $question->video_link ?? null,
                ];
            } else {
                $questions[] = [
                    'id' => $question->id,
                    'text' => $question->text,
                    'media' => (object)questionMedia($question),
                    'audio' => (object)questionAudio($question),
                    'video' => (object)questionVideo($question),
                    'question_feedback' => $question->question_feedback,
                    'is_true' => (bool)$question->is_true,
                    'audio_link' => $question->audio_link ?? null,
                    'video_link' => $question->video_link ?? null,
                ];
            }
        }

        return [
            'id' => $trueFalseData->id,
            'description' => $trueFalseData->description,
            'true_false_type' => $trueFalseData->TrueFalseType->slug,
            'questions' => $questions

        ];
    }


    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'date' => 'date',
            'SN' => 'SN'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
