<?php


namespace App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\GeneralQuizzes\Enums\GeneralQuizTypeEnum;
use App\OurEdu\GeneralQuizzes\Enums\QuestionsPublicStatusesEnums;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz ;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizQuestionBank;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuizStudentAnswer;
use App\OurEdu\Users\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class QuestionBankRepository implements QuestionBankRepositoryInterface
{
    use Filterable;

    public function create(array $data)
    {
        return GeneralQuizQuestionBank::query()->create($data);
    }


    public function update(int $id, array $data)
    {
        $question = GeneralQuizQuestionBank::query()->findOrFail($id);
        $question->update($data);

        return $question->refresh();
    }

    public function findGeneralQuizQuestion(GeneralQuiz $generalQuiz, int $questionId)
    {
        return $generalQuiz->questions()->where('id', $questionId)->with('questions')->firstOrFail();
    }


    public function findOrFail($questionBankId): ?GeneralQuizQuestionBank
    {
        return GeneralQuizQuestionBank::findOrFail($questionBankId);
    }


    public function updateOrCreateAnswer($question, $data)
    {
        if ($answer = $question->studentAnswers()->where('student_id', $data['student_id'])->first()) {
            $answer->update($data);
        } else {
            $answer = GeneralQuizStudentAnswer::create($data);
        }

        if (isset($data['details']) && count($data['details'])>0) {
            $answer->details()->delete();
            foreach ($data['details'] as $detail) {
                $answer->details()->create($detail);
            }
        }else{
            $answer->details()->delete();
        }

        return $answer;
    }

    /**
     * @param GeneralQuiz $quiz
     * @param string|null $publicStatus
     * @return Collection| LengthAwarePaginator
     */
    public function getAvailableBankQuestion(GeneralQuiz $quiz, string $publicStatus = null): Collection| LengthAwarePaginator
    {
        $methodName = ($publicStatus ?? "private") . 'AvailableBankQuestion';

        if (method_exists($this, $methodName)) {
            return $this->{$methodName}($quiz, Auth::user());
        }

        return new Collection();
    }

    /**
     * @param GeneralQuiz $quiz
     * @param User $user
     * @return LengthAwarePaginator
     */
    private function privateAvailableBankQuestion(GeneralQuiz $quiz, User $user): LengthAwarePaginator
    {
        return  GeneralQuizQuestionBank::query()
            ->whereNotNull('slug')
            ->where('subject_id' , $quiz->subject_id)
            ->whereIn('subject_format_subject_id',$quiz->subject_sections)
            ->whereDoesntHave('generalQuiz',function($query) use ($quiz){
                $query->where('general_quiz_id',$quiz->id);
            })
            ->where("created_by", "=", $user->id)
            ->where("public_status", "=", QuestionsPublicStatusesEnums::PRIVATE)
            ->paginate(env('PAGE_LIMIT', 20));
    }

    /**
     * @param GeneralQuiz $quiz
     * @param User $user
     * @return Collection| LengthAwarePaginator
     */
    private function publicAvailableBankQuestion(GeneralQuiz $quiz, User $user): Collection| LengthAwarePaginator
    {
        $quizSubject = $quiz->subject()
            ->with(["branchQuestionsPermissions" => function (BelongsToMany $school) use ($quiz) {
                $school->where("school_account_branches.id", "=", $quiz->branch_id);
            }])
            ->first();

        $questions = GeneralQuizQuestionBank::query()
            ->whereNotNull('slug')
            ->when(
                $quiz->quiz_type != GeneralQuizTypeEnum::COURSE_HOMEWORK,
                function (Builder $questionsQuery) use ($quiz) {
                    return $questionsQuery->where('subject_id' , $quiz->subject_id)
                        ->whereIn('subject_format_subject_id',$quiz->subject_sections);
                }
            )
            ->whereDoesntHave('generalQuiz', function($query) use ($quiz) {
                $query->where('general_quiz_id',$quiz->id);
            });

        if(
            isset($quizSubject)
            and $quizSubject->branchQuestionsPermissions->count()
            and (
                $quizSubject->branchQuestionsPermissions[0]->pivot->branch_scope
                or $quizSubject->branchQuestionsPermissions[0]->pivot->grade_scope
                or $quizSubject->branchQuestionsPermissions[0]->pivot->school_scope
            )
        ) {
            $questions->where(function (Builder $nestedQuery) use ($quiz, $quizSubject) {
                $nestedQuery->where(function(Builder $query) use ($quiz) {
                        $query->where("school_account_id", "=", $quiz->school_account_id)
                            ->whereIn("public_status", [QuestionsPublicStatusesEnums::SCHOOL, QuestionsPublicStatusesEnums::GRADE]);
                    });

                if($quizSubject->branchQuestionsPermissions[0]->pivot->branch_scope) {
                    $nestedQuery->orWhere(function (Builder $query) use ($quiz) {
                        $query->where("school_account_branch_id", "=", $quiz->branch_id)
                            ->where("public_status", "=", QuestionsPublicStatusesEnums::BRANCH);
                    });
                }

            });

            return $questions->jsonPaginate(env('PAGE_LIMIT', 20));
        }

        return new Collection();
    }

    public function findWhere($data = []): ?GeneralQuizQuestionBank
    {
        return GeneralQuizQuestionBank::where($data)->first();
    }
}
