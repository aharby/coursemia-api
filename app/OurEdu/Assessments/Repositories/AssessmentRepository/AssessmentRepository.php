<?php

namespace App\OurEdu\Assessments\Repositories\AssessmentRepository;

use App\OurEdu\Assessments\Enums\QuestionTypesEnums;
use App\OurEdu\Assessments\Models\Assessment;
use App\OurEdu\Assessments\Models\Questions\MultipleChoice\AssessmentMultipleChoiceOptions;
use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Assessments\Models\AssessmentQuestion;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use App\OurEdu\Users\UserEnums;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssessmentRepository implements AssessmentRepositoryInterface
{
    use Filterable;

    public Assessment $assessment;

    public function __construct(Assessment $assessment)
    {
        $this->assessment = $assessment;
    }

    public function create($data): Assessment
    {
        return $this->assessment->create($data);
    }

    public function update($data): bool
    {
        return $this->assessment->update($data);
    }

    public function delete(): bool
    {
        return $this->assessment->delete();
    }

    public function findOrFail($assessmentId): ?Assessment
    {
        return $this->assessment
            ->with(['questions', 'assessees', 'assessors', 'assessmentUsers'])
            ->findOrFail($assessmentId);
    }

    public function findOrFailByMultiFields($assessmentId, $filters): ?Assessment
    {
        return $this->assessment->where($filters)->findOrFail($assessmentId);
    }

    public function getAssessment(): Assessment
    {
        return $this->assessment;
    }

    public function setAssessment(Assessment $assessment)
    {
        $this->assessment = $assessment;
        return $this;
    }

    public function saveAssessmentAssessors(Assessment $assessment, $assessorsIds)
    {
        $assessment->assessors()->sync($assessorsIds);
        return $assessment;
    }

    public function saveAssessmentAssessees(Assessment $assessment, $assesseesIds)
    {
        $assessment->assessees()->sync($assesseesIds);
        return $assessment;
    }

    public function saveAssessmentViewers(Assessment $assessment, $usersIds)
    {
        $assessment->resultViewers()->sync($usersIds);
        return $assessment;
    }


    public function listAssessmentManagerAssessments($filters = [])
    {
        return $this->orderedAssessments($filters)
            ->paginate(env("PAGE_LIMIT", 20));
    }

    public function listAssessmentManagerAssessmentsReport($isPaginate = true, $filters = [])
    {
        $query = $this->orderedAssessments($filters)
            ->where('start_at', '<=', now());
        return $isPaginate ? $query->paginate(env("PAGE_LIMIT", 20)) : $query->get();
    }

    public function listSchoolAdminAssessmentsReport($isPaginate = true, $filters = [])
    {
        $query = $this->assessment->query();
        $query = count($filters) > 0 ? $this->assessmentFilters($query, $filters) : $query;
        $query = $query->where('school_account_id', auth()->user()->schoolAdmin->current_school_id)
            ->orderByDesc("start_at")
            ->orderByDesc("id")
            ->where('start_at', '<=', now());
        return $isPaginate ? $query->paginate(env("PAGE_LIMIT", 20))->withQueryString() : $query->get();
    }


    /**
     * @return Builder
     */
    private function orderedAssessments($filters = [])
    {
        $query = $this->assessment->query();
        $query = count($filters) > 0 ? $this->assessmentFilters($query, $filters) : $query;
        return $query->where('created_by', auth()->user()->id)
            ->orderByDesc("start_at")
            ->orderByDesc("id");
    }

    private function assessmentFilters($query, $filters = [])
    {
        return $query->when(isset($filters["assessor_type"]), function ($q) use ($filters) {
            $q->where('assessor_type', $filters['assessor_type']);
        })
            ->when(isset($filters["assessee_type"]), function ($q) use ($filters) {
                $q->where('assessee_type', $filters['assessee_type']);
            })
            ->when(isset($filters["from_date"]) && !isset($filters["to_date"]), function ($q) use ($filters) {
                $q->whereDate('start_at', '>=', Carbon::parse($filters['from_date'])->format('Y-m-d H-i-s'));
            })
            ->when(!isset($filters["from_date"]) && isset($filters["to_date"]), function ($q) use ($filters) {
                $q->whereDate('start_at', '<=', Carbon::parse($filters['to_date'])->format('Y-m-d H-i-s'));
            })
            ->when(isset($filters["from_date"]) && isset($filters["to_date"]), function ($q) use ($filters) {
                $q->whereBetween(
                    'start_at',
                    [
                        Carbon::parse($filters['from_date'])->format('Y-m-d H-i-s'),
                        Carbon::parse($filters['to_date'])->addDay()->subMinutes(1)->format('Y-m-d H-i-s')
                    ]
                );
            });
    }

    private function getUserSchoolBranches($user)
    {
        if ($user->type == UserEnums::SCHOOL_ACCOUNT_MANAGER) {
            $branches = $user->schoolAccount()->firstOrFail()
                ->branches()->pluck("school_account_branches.id")->toArray();
        } elseif ($user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $branches = $user->branches()->pluck("school_account_branches.id")->toArray();
        } else {
            $branches = [$user->schoolAccountBranchType->id];
        }
        return $branches;
    }

    public function listAssessmentReportForResultViewers($isPaginate = true, $filters = [])
    {
        $user = auth()->user();
        $query = Assessment::query()->with('assessors', 'authResultViewer')
            ->whereHas("resultViewers", function (Builder $users) use ($user) {
                $users->where("user_id", "=", $user->id);
            });

        $query = $this->assessmentFilters($query, $filters);

        if ($user->type !== UserEnums::ASSESSMENT_MANAGER) {
            $branches = $this->getUserSchoolBranches($user);
            $query = $query->whereHas('assessors', function ($assessorQuery) use ($branches) {
                $assessorQuery->whereHas("branches", function (Builder $builder) use ($branches) {
                    $builder->whereIn("school_account_branches.id", $branches);
                })->orWhereHas('schoolAccount.branches', function (Builder $builder) use ($branches) {
                    $builder->whereIn("school_account_branches.id", $branches);
                })->orWhereIn('branch_id', $branches);
            });

            if (in_array($user->type, [UserEnums::SCHOOL_INSTRUCTOR, UserEnums::EDUCATIONAL_SUPERVISOR])) {
                $method = $user->type == UserEnums::SCHOOL_INSTRUCTOR ? 'schoolInstructor' : 'educationalSupervisor';
                $query = $this->{$method . 'AssessmentReport'}($query, $user);
            }
        }

        $query = $query->where('start_at', '<=', now())
            ->where('published_before', true)
            ->orderByDesc("start_at")
            ->orderByDesc("id");

        return $isPaginate ? $query->paginate(env("PAGE_LIMIT")) : $query->get();
    }

    public function schoolInstructorAssessmentReport($query, $user)
    {
        $instructorSubjects = $user->schoolInstructorSubjects()->pluck('subjects.id')->toArray();
        return $query->where(function ($q) use ($instructorSubjects) {
            $q->where(function ($query) use ($instructorSubjects) {
                $query->where('assessor_type', UserEnums::SCHOOL_INSTRUCTOR)
                    ->whereHas(
                        'assessors.schoolInstructorSubjects',
                        function ($assessorQuery) use ($instructorSubjects) {
                            $assessorQuery->whereIn('subject_id', $instructorSubjects);
                        }
                    );
            })
                ->orWhere('assessor_type', '!=', UserEnums::SCHOOL_INSTRUCTOR);
        });
    }

    public function educationalSupervisorAssessmentReport($query, $user)
    {
        $subjects = $user->educationalSupervisorSubjects()->pluck('subjects.id')->toArray();
        return $query->where(function ($query) use ($subjects) {
            $query->where(function ($q) use ($subjects) {
                $q->where('assessor_type', UserEnums::EDUCATIONAL_SUPERVISOR)
                    ->whereHas('assessors.educationalSupervisorSubjects', function ($assessorQuery) use ($subjects) {
                        $assessorQuery->whereIn('subject_id', $subjects);
                    });
            })->orWhere(function ($q) use ($subjects) {
                $q->where('assessor_type', '=', UserEnums::SCHOOL_INSTRUCTOR)
                    ->whereHas('assessors.schoolInstructorSubjects', function ($assessorQuery) use ($subjects) {
                        $assessorQuery->whereIn('subject_id', $subjects);
                    });
            })->orWhereNotIn('assessor_type', [UserEnums::SCHOOL_INSTRUCTOR, UserEnums::EDUCATIONAL_SUPERVISOR]);
        });
    }

    /**
     * Retrieve assessment questions
     * @param Assessment $assessment
     * @return Collection
     */
    public function getAssessmentQuestions(Assessment $assessment)
    {
        return $assessment->questions()->with("question")->get();
    }


    public function returnQuestion(int $page, int $perPage = null): ?LengthAwarePaginator
    {
        $perPage = $perPage ?? AssessmentQuestion::$questionsPerPage;
        $routeName = 'assessments';

        $questions = $this->assessment->questions();


        $questions = $questions
            ->with(['question'])
            ->jsonPaginate($perPage, ['*', 'assessment_questions.question_id'], 'page', $page);

        return $questions;
    }

    /**
     * @param array $filters
     * @param bool $isPaginate
     * @return LengthAwarePaginator|Builder[]|Collection
     */
    public function assessmentsWithFilter(array $filters = [], bool $isPaginate = true)
    {
        $assessments = $this->orderedAssessments($filters)
            ->where('start_at', '<=', now())
            ->whereNotNull('published_at');

        if (isset($filters["branch"])) {
            $assessments
                ->whereHas("assessmentUsers", function (Builder $assessmentUsers) use ($filters) {
                    $assessmentUsers->whereHas('assessee', function (Builder $assessorQuery) use ($filters) {
                        $assessorQuery->whereHas("branches", function (Builder $builder) use ($filters) {
                            $builder->where("school_account_branches.id", "=", $filters["branch"]);
                        })->orWhereHas('schoolAccount.branches', function (Builder $builder) use ($filters) {
                            $builder->where("school_account_branches.id", "=", $filters["branch"]);
                        })->orWhere('branch_id', "=", $filters["branch"]);
                    });
                })
                ->with([
                    "assessmentBranchesScores" => function (BelongsToMany $branches) use ($filters) {
                        $branches->where("school_account_branches.id", "=", $filters["branch"]);
                    }
                ]);
        }

        return $isPaginate ? $assessments->paginate(env("PAGE_LIMIT", 20)) : $assessments->get();
    }

    /**
     * @param Assessment $assessment
     * @param array $filter
     * @param bool $isPaginate
     * @return LengthAwarePaginator|Builder[]|Collection
     */
    public function getAssessmentQuestionsWithFilter(
        Assessment $assessment,
        array $filter = [],
        bool $isPaginate = true
    ): LengthAwarePaginator|array|Collection {
        $relations = ["question", "category"];

        $questions = $assessment->questions();

        if (isset($filter["branch"])) {
            $relation = [
                "branchScores" => function (BelongsToMany $branches) use ($filter) {
                    $branches->where("school_account_branches.id", "=", $filter["branch"]);
                }
            ];

            $relations = array_merge($relations, $relation);
        }

        $questions->with($relations);

        return $isPaginate ? $questions->paginate(env("PAGE_LIMIT", 20)) : $questions->get();
    }

    public function getAssessmentWithQuestion($isPaginate = true, $filters = [])
    {
        $query = $this->assessment->query()->where('created_by', auth()->user()->id)
            ->where('start_at', '<=', now())
            ->whereNotNull('published_at')
            ->with('questions', function ($query) use ($filters) {
                $query->where('slug', '!=', QuestionTypesEnums::ESSAY_QUESTION)
                    ->when(isset($filters['branch_id']),function ($branch)  use ($filters) {
                        $branch->whereHas('branchScores',function ($branchScores) use ($filters) {
                            $branchScores->where('school_account_branches.id',$filters['branch_id']);
                        });
                    })
                    ->with('assessorsAnswers', function ($qu) use ($filters) {
                        $qu->whereHas('assessmentUser', function ($q) use ($filters) {
                            $q->finished()
                                ->when(isset($filters['branch_id']),function ($branch)  use ($filters) {
                                    $branch->whereHas('assessee',function ($user) use ($filters) {
                                        $this->queryUserBranch($user,$filters['branch_id']);
                                    });
                                });
                            $this->filterAssessmentUserByStartAndEndDate($q,$filters);
                               });
                    });
            });

           $this->filterAssessmentUserByAssessorAndAssesse($query, $filters);

            $query->withCount(['assessmentUsers' => function($q)use($filters){
                $q->finished()
                     ->when(isset($filters['branch_id']),function ($branch)  use ($filters) {
                         $branch->whereHas('assessee',function ($user) use ($filters) {
                             $this->queryUserBranch($user,$filters['branch_id']);
                         });
                     });
                $this->filterAssessmentUserByStartAndEndDate($q,$filters);
            }, 'questions'=> function($query) use ($filters) {
                $query->where('slug', '!=', QuestionTypesEnums::ESSAY_QUESTION)
                    ->when(isset($filters['branch_id']),function ($branch)  use ($filters) {
                        $branch->whereHas('branchScores',function ($branchScores) use ($filters) {
                            $branchScores->where('school_account_branches.id',$filters['branch_id']);
                        });
                    });
            }])
            ->orderByDesc("start_at")
            ->orderByDesc("id");

        return $isPaginate ? $query->paginate(env("PAGE_LIMIT", 20)) : $query->get();
    }

    private function filterAssessmentUserByStartAndEndDate($q, $filters)
    {
        $q->when(isset($filters["from_date"]) && !isset($filters["to_date"]), function ($q) use ($filters) {
            $q->whereDate('end_at', '>=', Carbon::parse($filters['from_date'])->format('Y-m-d H-i-s'));
        })
            ->when(!isset($filters["from_date"]) && isset($filters["to_date"]), function ($q) use ($filters) {
                $q->whereDate('end_at', '<=', Carbon::parse($filters['to_date'])->format('Y-m-d H-i-s'));
            })
            ->when(isset($filters["from_date"]) && isset($filters["to_date"]), function ($q) use ($filters) {
                $q->whereBetween('end_at', [
                    Carbon::parse($filters['from_date'])->format('Y-m-d H-i-s'),
                    Carbon::parse($filters['to_date'])->addDay()->subMinutes(1)->format('Y-m-d H-i-s')
                ]);
            });
    }
    private function filterAssessmentUserByAssessorAndAssesse($q, $filters)
    {
        $q->when(isset($filters["assessor_type"]), function ($q) use ($filters) {
            $q->where('assessor_type', $filters['assessor_type']);

        })
        ->when(isset($filters["assessee_type"]), function($q) use ($filters) {
            $q->where('assessee_type', $filters['assessee_type']);
        });
    }

    private function queryUserBranch($query,$branch_id)
    {
        return $query->where(function (Builder $userBranches) use ($branch_id) {
            $userBranches->whereHas("branches", function (Builder $builder) use ($branch_id) {
                $builder->where("school_account_branches.id", $branch_id); // for educational supervisor
            })->orWhereHas('schoolAccount.branches', function (Builder $builder) use ($branch_id) {
                $builder->where("school_account_branches.id", $branch_id); // for school manager
            })->orWhere('branch_id', $branch_id);
        });
    }
    public function questionAnswersPercentage(int $assessmentId)
    {
        return AssessmentQuestion::query()
            ->where('assessment_id', $assessmentId)
            ->with('question')
            ->withCount('assessorsAnswers')
            ->get();
    }
}
