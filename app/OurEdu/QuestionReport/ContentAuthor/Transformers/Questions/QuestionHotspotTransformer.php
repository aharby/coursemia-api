<?php


namespace App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\ResourceSubjectFormats\Models\HotSpot\HotSpotData;
use App\OurEdu\Users\UserEnums;
use League\Fractal\TransformerAbstract;

class QuestionHotspotTransformer extends TransformerAbstract
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
     * @param HotSpotData $hotSpotData
     * @return array
     */
    public function transform(HotSpotData $hotSpotData)
    {
        $questions = [];
        foreach ($hotSpotData->questions as $question) {

            $questionsData = [
                'id' => $question->id,
                'question' => $question->question,
                'media' => (object) questionMedia($question)
            ];
            $questions[] = $questionsData;

        }
        $options = [];
        foreach ($hotSpotData->options as $answer) {

            $options[] = [
                'id' => $answer->id,
                'option' => $answer->option,
            ];

        }
        return [
            'id' => $hotSpotData->id,
            'description' => $hotSpotData->description,
            'questions' => $questions,
            'options' => $options

        ];
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

