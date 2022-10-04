<?php


namespace App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions;


use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\MultipleChoice\MultipleChoiceData;
use App\OurEdu\Users\UserEnums;
use League\Fractal\TransformerAbstract;

class QuestionMultipleChoiceTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
        'actions'

    ];
    protected array $availableIncludes = [
    ];

    private $params;

    public function __construct($params = [])
    {
        $this->params = $params;
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

            $optionsData['is_correct_answer'] = (bool)$option->is_correct_answer;
            $options[] = $optionsData;
        }
        $questions = [
            'id' => $question->id,
            'slug' => $question->slug,
            'question' => $question->question,
            'media' => (object) questionMedia($question),
            'options' => $options,
        ];

        return $questions;
    }
    public function includeActions($questionData)
    {
        $actions = [];
        if (auth()->user()->type == UserEnums::CONTENT_AUTHOR_TYPE  && $questionData->task_id) {
            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.contentAuthor.question.report.tasks.fillResource', ['id' => $questionData->task_id]),
                'label' => trans('task.Fill Resource'),
                'key' => APIActionsEnums::FILL_RESOURCE,
                'method' => 'POST'
            ];
            if ($questionData->related_task) {
                $task = $questionData->related_task;
                if ($task->is_done == 0 && $task->contentAuthors()->where('content_authors.id', auth()->user()->contentAuthor->id)->exists()) {
                    $actions[] = [
                        'endpoint_url' => buildScopeRoute('api.contentAuthor.question.report.tasks.markTaskAsDone', ['id' => $task->id]),
                        'label' => trans('task.Done'),
                        'key' => APIActionsEnums::MARK_TASK_AS_DONE,
                        'method' => 'GET'
                    ];
                }
            }
        }
        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }

}

