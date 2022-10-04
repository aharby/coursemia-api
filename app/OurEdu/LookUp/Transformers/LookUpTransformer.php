<?php


namespace App\OurEdu\LookUp\Transformers;

use App\OurEdu\AcademicYears\AcademicYear;
use App\OurEdu\AcademicYears\Transformers\AcademicYearLookUpTransformer;
use App\OurEdu\AcademicYears\Transformers\DifficultyLevelsLookUpTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Countries\Country;
use App\OurEdu\Countries\Transformers\CountryLookUpTransformer;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\EducationalSystems\Transformers\EducationalSystemLookUpTransformer;
use App\OurEdu\GarbageMedia\MediaEnums;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\GradeClasses\Transformers\GradeClassLookUpTransformer;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Option;
use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClass\ClassroomClass;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\Schools\School;
use App\OurEdu\Schools\Transformers\SchoolLookUpTransformer;
use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class LookUpTransformer extends TransformerAbstract
{
    protected array $defaultIncludes = [];

    protected array $availableIncludes = [
        'countries',
        'educationalSystems',
        'schools',
        'classes',
        'academicYear',
        'difficultyLevel',
        'allowedQuestionsCountForExam',
        'subjects',
        'classrooms',
        'classroomClasses',
        'classroomClassSessions',
        'quizTypes',
        'quizTimes',
        'assignedSubjects',
        'mediaTypes',
        'schoolBranches',
        'schoolGradeClasses'
    ];

    private $param;
    private $user;

    public function __construct(array $param)
    {
        $this->param = $param;
        $this->user = Auth::guard('api')->user();
    }

    /**
     * @return array
     */
    public function transform()
    {
        return [
            'id' => Str::uuid()
        ];
    }

    public function includeCountries()
    {
        $countries = Country::get();
        return $this->collection($countries, new CountryLookUpTransformer(), ResourceTypesEnums::COUNTRY);
    }

    public function includeEducationalSystems()
    {
        $educationalSystems = new EducationalSystem();
        if (isset($this->param['country_id'])) {
            $educationalSystems = $educationalSystems->where('country_id', $this->param['country_id']);
        }
        $educationalSystems = $educationalSystems->get();
        return $this->collection(
            $educationalSystems,
            new EducationalSystemLookUpTransformer(),
            ResourceTypesEnums::EDUCATIONAL_SYSTEM
        );
    }

    public function includeSchools()
    {
        $school = new School();

        if (isset($this->param['country_id'])) {
            $school = $school->where('country_id', $this->param['country_id']);
        }

        if (isset($this->param['educational_system_id'])) {
            $school = $school->where('educational_system_id', $this->param['educational_system_id']);
        }

        $school = $school->get();
        return $this->collection(
            $school,
            new SchoolLookUpTransformer(),
            ResourceTypesEnums::SCHOOL
        );
    }

    public function includeClasses()
    {
        $classes =  GradeClass::query();
        if (isset($this->param['country_id'])) {
            $classes = $classes->where('country_id', $this->param['country_id']);
        }

        if (isset($this->param['educational_system_id'])) {
            $classes = $classes->where('educational_system_id', $this->param['educational_system_id']);
        }

        if ($this->user && $this->user->type == UserEnums::SCHOOL_INSTRUCTOR) {
            $instructorGradeClasses = $this->user->schoolInstructorSubjects->pluck('grade_class_id')->toArray();
            $classes = $classes->whereIn('id', $instructorGradeClasses);
        }
        if ($this->user && $this->user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $branchesIDs = [];
            if (isset($this->param['branch_id'])) {
                $branchesIDs = [$this->param['branch_id']];
            } else {
                $branchesIDs = $this->user->branches()->get()->pluck("id")->toArray() ?? [];
            }

            $educationalSupervisorSubjects = $this->user->educationalSupervisorSubjects;
            $assignedGradeClassesIds =array_unique($educationalSupervisorSubjects->pluck('grade_class_id')->toArray());
            $classes = $classes->whereIn('id', $assignedGradeClassesIds)
                ->whereHas("branchEducationalSystemGradeClass.branchEducationalSystem.branch", function (Builder $branch) use ($branchesIDs) {
                    $branch->whereIn("id", $branchesIDs);
                });
        }
        $classes = $classes->get();
        return $this->collection(
            $classes,
            new GradeClassLookUpTransformer(),
            ResourceTypesEnums::GRADE_CLASS
        );
    }

    public function includeAcademicYear()
    {
        $academicYears = Option::where('type', OptionsTypes::ACADEMIC_YEAR)->get();
        return $this->collection(
            $academicYears,
            new AcademicYearLookUpTransformer(),
            ResourceTypesEnums::ACADEMIC_YEAR
        );
    }

    public function includeDifficultyLevel()
    {
        $difficultyLevels = Option::where('type', OptionsTypes::RESOURCE_DIFFICULTY_LEVEL)->get();
        return $this->collection(
            $difficultyLevels,
            new DifficultyLevelsLookUpTransformer(),
            ResourceTypesEnums::RESOURCE_DIFFICULTY_LEVEL
        );
    }

    public function includeAllowedQuestionsCountForExam()
    {
        $allowedQuestionsCount = allowedQuestionsCountForExam();
        return $this->collection(
            $allowedQuestionsCount,
            new AllowedQuestionsCountLookUpTransformer(),
            ResourceTypesEnums::ALLOWED_QUESTION_COUNT
        );
    }

    public function includeSubjects()
    {
        $subjects = new Subject();
        if ($this->user && $this->user->type == UserEnums::SCHOOL_INSTRUCTOR) {
            $instructorSubjects = $this->user->schoolInstructorSubjects;

            if (isset($this->param['grade_class_id'])) {
                $instructorSubjects = $instructorSubjects->where('grade_class_id', $this->param['grade_class_id']);
            }
            return $this->collection(
                $instructorSubjects,
                new SubjectLookUpTransformer(),
                ResourceTypesEnums::SUBJECT
            );
        }
        if ($this->user && $this->user->type == UserEnums::SME_TYPE) {
            $subjects = $subjects->where('sme_id', $this->user->id);
        }

        if ($this->user && $this->user->type == UserEnums::STUDENT_TYPE) {
            $studentSubjects = $this->user->student->subjects;

            return $this->collection(
                $studentSubjects,
                new SubjectLookUpTransformer(),
                ResourceTypesEnums::SUBJECT
            );
        }

        $subjects = $subjects->get(['name','id']);
        return $this->collection(
            $subjects,
            new SubjectLookUpTransformer(),
            ResourceTypesEnums::SUBJECT
        );
    }

    public function includeClassrooms()
    {
        if ($this->user && $this->user->type == UserEnums::SCHOOL_INSTRUCTOR) {
            $classrooms = [];
            $classroomsIDs = $this->user->schoolInstructorSessions()->distinct()->pluck('classroom_id')->toArray();

            if (count($classroomsIDs)) {
                $classrooms = Classroom::query()->whereIn("id", $classroomsIDs);

                if (isset($this->param['grade_class_id'])) {
                    $classrooms->whereHas("branchEducationalSystemGradeClass.gradeClass", function (Builder $gradeClass) {
                        $gradeClass->where("id", "=", $this->param['grade_class_id']);
                    });
                }

                $classrooms = $classrooms->get(['name', 'id']);
            }

            return $this->collection($classrooms, new ClassroomLookUptransformer(),
                ResourceTypesEnums::CLASSROOM);
        }

        if ($this->user && $this->user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $classrooms = [];

            if (isset($this->param['branch_id'])) {
                $branchesIDs = [$this->param['branch_id']];
            } else {
                $branchesIDs = $this->user->branches()->get()->pluck("id")->toArray() ?? [];
            }

            if (count($branchesIDs)) {

                $educationalSupervisorSubjects = $this->user->educationalSupervisorSubjects;
                $assignedGradeClassesIds =array_unique($educationalSupervisorSubjects->pluck('grade_class_id')->toArray());
                $classes = GradeClass::query()->whereIn('id', $assignedGradeClassesIds)->pluck("id")->toArray();

                $classrooms = Classroom::query()->whereIn("branch_id", $branchesIDs);

                    $classrooms->whereHas("branchEducationalSystemGradeClass.gradeClass", function (Builder $gradeClass) use ($classes) {
                        if (isset($this->param['grade_class_id'])) {
                            $gradeClass->where("id", "=", $this->param['grade_class_id']);
                        }

                        $gradeClass->whereIn("id", $classes);
                    });

                $classrooms = $classrooms->get(['name', 'id']);
            }

            return $this->collection($classrooms, new ClassroomLookUptransformer(),
                ResourceTypesEnums::CLASSROOM);
        }

        $classrooms = Classroom::get(['name','id']);
        return $this->collection(
            $classrooms,
            new ClassroomLookUptransformer(),
            ResourceTypesEnums::CLASSROOM
        );
    }

    public function includeClassroomClasses()
    {
        $classroomClasses = new ClassroomClass();
        if (isset($this->param['classroom_id'])) {
            $classroomClasses = $classroomClasses->where('classroom_id', $this->param['classroom_id']);
        }
        return $this->collection(
            $classroomClasses->get(),
            new ClassroomClassLookUpTransformer(),
            ResourceTypesEnums::CLASSROOM_CLASS
        );
    }

    public function includeClassroomClassSessions()
    {
        $classroomClassSessions = new ClassroomClassSession();
        if (isset($this->param['classroom_id'])) {
            $classroomClassSessions = $classroomClassSessions
                ->where('classroom_id', $this->param['classroom_id']);
        }
        if (isset($this->param['classroom_class_id'])) {
            $classroomClassSessions = $classroomClassSessions
                ->where('classroom_class_id', $this->param['classroom_class_id']);
        }
        return $this->collection(
            $classroomClassSessions->get(),
            new ClassroomClassSessionLookUpTransformer(),
            ResourceTypesEnums::CLASSROOM_CLASS_SESSION
        );
    }

    public function includeQuizTypes()
    {
        $quizTypes = QuizTypesEnum::getAllQuizTypes();
        return $this->collection(
            $quizTypes,
            new QuizTypesLookUpTransformer(),
            ResourceTypesEnums::QUIZ_TYPE
        );
    }

    public function includeQuizTimes()
    {
        $quizTimes = QuizTimesEnum::getAllQuizTimes();
        return $this->collection(
            $quizTimes,
            new QuizTimesLookUpTransformer(),
            ResourceTypesEnums::QUIZ_TIME
        );
    }

    public function includeAssignedSubjects()
    {
        $subjects = new Subject();
        if ($this->user && $this->user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $educationalSupervisorSubjects = $this->user->educationalSupervisorSubjects;
            if (isset($this->param['grade_class_id'])) {
                $educationalSupervisorSubjects = $educationalSupervisorSubjects->where('grade_class_id', $this->param['grade_class_id']);
            }
            return $this->collection(
                $educationalSupervisorSubjects,
                new SubjectLookUpTransformer(),
                ResourceTypesEnums::SUBJECT
            );
        }
    }
    public function includeMediaTypes()
    {
        return $this->collection(MediaEnums::getMediaTypes(), new MediaTypesTransformer(), ResourceTypesEnums::MEDIA_TYPES);
    }

    public function includeSchoolBranches()
    {
        if ($this->user and $this->user->type == UserEnums::SCHOOL_INSTRUCTOR) {
            $branches = $this->user->branch->schoolAccount->branches ?? [];

            return $this->collection($branches, new SchoolBranchesTransformer(), ResourceTypesEnums::SCHOOL_BRANCHES);
        }

        if ($this->user and $this->user->type == UserEnums::EDUCATIONAL_SUPERVISOR) {
            $branches = $this->user->branches ?? [];

            return $this->collection($branches, new SchoolBranchesTransformer(), ResourceTypesEnums::SCHOOL_BRANCHES);
        }

        if ($this->user and $this->user->type == UserEnums::ASSESSMENT_MANAGER) {
            $branches = $this->user->school->branches ?? [];

            return $this->collection($branches, new SchoolBranchesTransformer(), ResourceTypesEnums::SCHOOL_BRANCHES);
        }
    }

    public function includeSchoolGradeClasses()
    {
        $branch = $branch ?? $this->user->schoolAccountBranchType;
        $gradeClasses = $branch->branchEducationalSystem()->with('gradeClasses')->get()->pluck('gradeClasses')->flatten();
        return $this->collection(
            $gradeClasses,
            new GradeClassLookUpTransformer(),
            ResourceTypesEnums::GRADE_CLASS
        );
    }

}
