<?php


namespace App\OurEdu\QuestionReport\ContentAuthor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\LearningResources\Enums\LearningResourcesEnums;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions\QuestionDragDropTransformer;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions\QuestionMatchingTransformer;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions\QuestionMultiMatchingTransformer;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions\QuestionMultipleChoiceTransformer;
use App\OurEdu\QuestionReport\ContentAuthor\Transformers\Questions\QuestionTrueFalseTransformer;
use App\OurEdu\QuestionReport\Models\QuestionReportTask;
use App\OurEdu\Users\UserEnums;
use League\Fractal\TransformerAbstract;

class QuestionReportTaskTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [
         'actions'
    ];
    protected array $availableIncludes = [

    ];


    public function transform(QuestionReportTask $task)
    {
        return [
            'id' => (int)$task->id,
            'title' => (string)$task->title,
            'note' => (string)$task->note,
            'is_active' => (bool)$task->is_active,
            'is_done' => (bool)$task->is_done,
            'is_expired' => (bool)$task->is_expired,
            'is_assigned' => (bool)$task->is_assigned,
            'due_date' => (int)$task->due_date,
            'question_type' => (string)$task->question_type,
            'created_at' => (string)$task->created_at,
            'subject_id' => (string)$task->subject_id,
            'resource_subject_format_subject_id' => (string)$task->resource_subject_format_subject_id,
            'subject_format_subject_id' => (string)$task->subject_format_subject_id,

        ];
    }

    public function includeActions($task)
    {
        $actions = [];
        if (auth()->user()->type == UserEnums::CONTENT_AUTHOR_TYPE) {
            if ($task->is_assigned == 0) {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.contentAuthor.question.report.tasks.pullTask', ['id' => $task->id]),
                    'label' => trans('task.Pull Task'),
                    'key' => APIActionsEnums::PULL_TASK,
                    'method' => 'POST'
                ];
            } else {
                $actions[] = [
                    'endpoint_url' => buildScopeRoute('api.contentAuthor.question.report.tasks.get.fillResource', ['id' => $task->id]),
                    'label' => trans('task.Get Fill Task'),
                    'key' => APIActionsEnums::FILL_RESOURCE,
                    'method' => 'GET'
                ];
            }
        }

        if (count($actions) > 0) {
            return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
        }
    }


//    public function includeQuestionData(QuestionReportTask $task)
//    {
//        $question = $task->questionable()->get()->first();
//        $question->slug = $task->slug;
//        $slug = $task->slug;
//        switch ($slug) {
//            case LearningResourcesEnums::TRUE_FALSE:
//                return $this->item($question, new QuestionTrueFalseTransformer(),
//                    ResourceTypesEnums::QUESTION_EXAM_DATA);
//                break;
//            case LearningResourcesEnums::MULTI_CHOICE:
//
//                return $this->item($question, new QuestionMultipleChoiceTransformer(),
//                    ResourceTypesEnums::QUESTION_EXAM_DATA);
//                break;
//
//            case LearningResourcesEnums::DRAG_DROP:
//                return $this->item($question, new QuestionDragDropTransformer(),
//                    ResourceTypesEnums::QUESTION_EXAM_DATA);
//                break;
//            case LearningResourcesEnums::MATCHING:
//
//                return $this->item($question, new QuestionMatchingTransformer(),
//                    ResourceTypesEnums::QUESTION_EXAM_DATA);
//                break;
//            case LearningResourcesEnums::MULTIPLE_MATCHING:
//                return $this->item($question, new QuestionMultiMatchingTransformer(),
//                    ResourceTypesEnums::QUESTION_EXAM_DATA);
//                break;
//        }
//
//
//    }
}
