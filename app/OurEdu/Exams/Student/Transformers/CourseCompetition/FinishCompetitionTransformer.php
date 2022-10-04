<?php

namespace App\OurEdu\Exams\Student\Transformers\CourseCompetition;

use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Exams\Models\Competitions\CompetitionQuestionStudent;
use App\OurEdu\Exams\Models\Exam;
use App\OurEdu\Exams\Instructor\Transformers\CompetitionOrderdListTransformer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class FinishCompetitionTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = ['competition_group_order','students'];

    public function transform($data)
    {
        $currentUser = $data->students->flatten()->keyBy('id')->get($data->user->student->id);
        $totalCorrectAnswers = $currentUser?->pivot->result ?? 0;
        $examQuestionsCount =  $data->exam->questions()->count();

        return [
            'id'=> (int) $data->user->id?? Str::uuid(),
            'profile_picture' => (string) imageProfileApi($currentUser->user->profile_picture),
            'name'=> (string) $data->user?->name,
            'total_correct_answers' => (int) $totalCorrectAnswers,
            "result" =>  (string) $examQuestionsCount > 0 ? $totalCorrectAnswers .'/'.$examQuestionsCount:0,
            'student_rank' => (string) ($currentUser->pivot->is_finished) ? getOrdinal($currentUser->pivot->rank):trans("exam.calculating rank in progress"),
            "avg_result" =>  (float) $examQuestionsCount > 0 ? $totalCorrectAnswers /$examQuestionsCount * 100 .'%':'0%',
            'exam_id' => (int) $data->exam->id,
            'exam_title' => (string)  $data->exam->title,
            'start_time' => $data->exam->start_time,
            'difficulty_level' => (string) $data->exam->difficulty_level,
            'questions_number' => (int) $data->exam->questions_number,
            'allStudentsCount'=>  (int) $data->allStudentsCompetition,
            'allFinishedStudentsCount' => (int) $data->finishedStudentsInCompetition,
            'NotFinishedStudentsCount' => (int) $data->allStudentsCompetition - $data->finishedStudentsInCompetition,
        ];
    }

    public function includeCompetitionGroupOrder($data)
    {
        return $this->collection($data->studentBulkOrderInCompetition, new CompetitionOrderdListTransformer($data->exam),ResourceTypesEnums::COMPETITION_GROUP_ORDER);
    }

    public function includeStudents($data)
    {
        return $this->collection($data->studentOrderInCompetition, new CourseCompetitionStudentsTransformer($data->exam),ResourceTypesEnums::COMPETITION_STUDENT);
    }
}
