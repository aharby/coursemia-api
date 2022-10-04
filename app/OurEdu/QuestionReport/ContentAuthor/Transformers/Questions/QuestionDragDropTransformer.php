<?php


namespace App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\DragDrop\DragDropData;
use App\OurEdu\Users\UserEnums;
use League\Fractal\TransformerAbstract;

class QuestionDragDropTransformer extends TransformerAbstract
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
                'media' => (object) questionMedia($question)
            ];
            $questionsData['answers'] = $question->correct_option_id;
            $questions[] = $questionsData;

        }
        $options = [];
        foreach ($dragDropData->options as $option) {


            $options[] = [
                'id' => $option->id,
                'option' => $option->option,
            ];

        }
        return [
            'id' => $dragDropData->id,
            'slug' => $dragDropData->slug,
            'description' => $dragDropData->description,
            'questions' => $questions,
            'options' => $options

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

    public function includeActions($questionData)
    {
        $actions = [];
        if (auth()->user()->type == UserEnums::CONTENT_AUTHOR_TYPE && $questionData->task_id) {
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

