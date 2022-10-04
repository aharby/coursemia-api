<?php


namespace App\OurEdu\SchoolAdmin\FormativeTest\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\EducationalSystems\EducationalSystem;
use App\OurEdu\GeneralQuizzes\Models\GeneralQuiz;
use App\OurEdu\GradeClasses\GradeClass;
use App\OurEdu\SchoolAdmin\FormativeTest\Requests\FormativeTestRequest;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CloneFormativeUseCase\CloneFormativeUseCaseInterface;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\CreateFormativeTestUseCase\CreateFormativeTestUseCaseInterface;
use App\OurEdu\SchoolAdmin\FormativeTest\UseCases\UpdateFormativeTestUseCase\UpdateFormativeTestUseCaseInterface;
use App\OurEdu\SchoolAdmin\GeneralQuizzes\Repositories\GeneralQuizRepository;
use App\OurEdu\Subjects\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FormativeTestController extends BaseController
{
    private $generalQuizRepository;
    private CreateFormativeTestUseCaseInterface $createFormativeTestUseCase;
    private UpdateFormativeTestUseCaseInterface $updateFormativeTestUseCase;
    private CloneFormativeUseCaseInterface $cloneFormativeUseCase;

    public function __construct(
        CreateFormativeTestUseCaseInterface $createFormativeTestUseCase,
        UpdateFormativeTestUseCaseInterface $updateFormativeTestUseCase,
        CloneFormativeUseCaseInterface $cloneFormativeUseCase
    )
    {
        $this->generalQuizRepository = new GeneralQuizRepository();
        $this->createFormativeTestUseCase = $createFormativeTestUseCase;
        $this->updateFormativeTestUseCase = $updateFormativeTestUseCase;
        $this->cloneFormativeUseCase = $cloneFormativeUseCase;
        $this->middleware('type:school_admin');
    }

    public function index()
    {
        $data = [];
        $data['formativeTests'] = $this->generalQuizRepository->ListFormativeTest();
        return view("school_admin.formativeTest.index", $data);
    }


    public function create()
    {
        $authUSer = Auth::user();
        $schoolsAccountsIDs = $authUSer->schoolAdminAssignedSchools()->pluck("school_accounts.id")->toArray();
        $data = [];


        $data['educationalSystems'] = EducationalSystem::query()
            ->with('translations')
            ->whereHas(
                'schoolAccounts',
                function (Builder $schoolAccounts) use ($schoolsAccountsIDs) {
                    $schoolAccounts->whereIn('school_accounts.id', $schoolsAccountsIDs);
                }
            )
            ->get()
            ->pluck("name", "id");

        return view("school_admin.formativeTest.create", $data);
    }

    public function store(FormativeTestRequest $request)
    {
        $useCase = $this->createFormativeTestUseCase->createFormativeTest($request->all());

        if (!isset($useCase['status']) or $useCase['status'] != 200) {
            $data = [];
            if ($request->has('grade_class_id')) {
                $data['subjects'] = Subject::query()
                ->where('grade_class_id', "=", $request->grade_class_id )
                ->pluck("name", "id")
                ->toArray();
            }

            return redirect()->back()->with([$data, "error" => $useCase['message']])->withInput($request->all());
        }

        return redirect(route('school-admin.formative-test.questions', $useCase['formative_test']))->with(["success" =>  $useCase['message']]);
    }

    public function edit(GeneralQuiz $formativeTest)
    {
        $authUSer = Auth::user();
        $schoolsAccountsIDs = $authUSer->schoolAdminAssignedSchools()->pluck("school_accounts.id")->toArray();
        $data = [];
        $data['formativeTest'] = $formativeTest;

        $data['educationalSystems'] = EducationalSystem::query()
            ->whereHas(
                'schoolAccounts',
                function (Builder $schoolAccounts) use ($schoolsAccountsIDs) {
                    $schoolAccounts->whereIn('school_accounts.id', $schoolsAccountsIDs);
                }
            )
            ->get()
            ->pluck("name", "id");

        $data['gradeClasses'] = GradeClass::query()
            ->whereHas(
                "schoolAccounts",
                function (Builder $schoolAccountQBuilder) use ($schoolsAccountsIDs) {
                    $schoolAccountQBuilder->whereIn("id", $schoolsAccountsIDs);
                }
            )
            ->where("educational_system_id", "=", $formativeTest->educational_system_id)
            ->get()
            ->pluck("title", "id")
            ->toArray();

        $data['subjects'] = Subject::query()
            ->where('grade_class_id', "=", $formativeTest->grade_class_id )
            ->pluck("name", "id")
            ->toArray();

            return view("school_admin.formativeTest.edit", $data);
    }

    public function update(FormativeTestRequest $request, GeneralQuiz $formativeTest)
    {
        $useCase = $this->updateFormativeTestUseCase->updateFormativeTest($request->all(), $formativeTest);

        if ($useCase['status'] != 200) {
            return redirect()->back()->with(["error" => $useCase['message']]);
        }

        return redirect(route('school-admin.formative-test.index'))->with(["success" =>  $useCase['message']]);
    }

    public function delete(GeneralQuiz $formativeTest)
    {
        $formativeTest->delete();

        return redirect()->back()->with(['success' => trans("app.Deleted Successfully")]);
    }

    public function publish(GeneralQuiz $formativeTest)
    {
        $useCase = $this->updateFormativeTestUseCase->publishFormativeTest($formativeTest);

        if ($useCase['status'] != 200) {
            return redirect()->back()->with(['error' => $useCase['message']]);
        }

        return redirect()->back()->with(["success" => $useCase['message']]);
    }

    public function getClone(GeneralQuiz $formativeTest)
    {
        $authUSer = Auth::user();
        $schoolsAccountsIDs = $authUSer->schoolAdminAssignedSchools()->pluck("school_accounts.id")->toArray();
        $data = [];
        $data['formativeTest'] = $formativeTest;

        $data['educationalSystems'] = EducationalSystem::query()
            ->whereHas(
                'schoolAccounts',
                function (Builder $schoolAccounts) use ($schoolsAccountsIDs) {
                    $schoolAccounts->whereIn('school_accounts.id', $schoolsAccountsIDs);
                }
            )
            ->get()
            ->pluck("name", "id");

        $data['gradeClasses'] = GradeClass::query()
            ->whereHas(
                "schoolAccounts",
                function (Builder $schoolAccountQBuilder) use ($schoolsAccountsIDs) {
                    $schoolAccountQBuilder->whereIn("id", $schoolsAccountsIDs);
                }
            )
            ->where("educational_system_id", "=", $formativeTest->educational_system_id)
            ->get()
            ->pluck("title", "id")
            ->toArray();

        $data['subjects'] = Subject::query()
            ->where('grade_class_id', "=", $formativeTest->grade_class_id)
            ->pluck("name", "id")
            ->toArray();

        $data['page_title'] = trans('formative_tests.clone formative test');

        return view("school_admin.formativeTest.clone", $data);
    }

    public function clone(FormativeTestRequest $request, GeneralQuiz $formativeTest)
    {
        $useCase = $this->cloneFormativeUseCase->clone($formativeTest, $request->all());

        if ($useCase['status'] != 200) {
            return redirect()->back()->withInput()->with(['error' => $useCase['message']]);
        }

        return redirect()->route('school-admin.formative-test.index')->with(["success" => $useCase['message']]);
    }
}
