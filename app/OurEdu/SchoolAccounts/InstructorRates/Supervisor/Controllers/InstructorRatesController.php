<?php
namespace App\OurEdu\SchoolAccounts\InstructorRates\Supervisor\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Ratings\Rating;
use App\OurEdu\SchoolAccounts\InstructorRates\Supervisor\Exports\AverageInstructorsRatesExport;
use App\OurEdu\SchoolAccounts\InstructorRates\Supervisor\Exports\InstructorRatesExport;
use App\OurEdu\SchoolAccounts\SchoolBranchSupervisor\Middleware\SchoolSupervisorMiddleware;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Users\Repository\UserRepositoryInterface;
use Maatwebsite\Excel\Facades\Excel;

class InstructorRatesController extends BaseController
{

    private $module;
    private $title;
    private $parent;
    /**
     * @var UserRepositoryInterface
    */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->module = 'instructorRates';
        $this->title = trans('app.instructorRates');
        $this->parent = ParentEnum::SCHOOL_SUPERVISOR;
        $this->userRepository = $userRepository;
        $this->middleware(SchoolSupervisorMiddleware::class);
    }


    public function getIndex()
    {
        $branch = auth()->user()->schoolAccountBranchType;

        $data['rows'] = $this->userRepository->getSchoolBranchInstructors($branch->id, ["schoolInstructorSubjects.gradeClass"]);
        $data['page_title'] =trans('app.View').' '.trans('instructors.Rates');
        return view($this->parent . '.' . $this->module . '.index', $data);
    }


    public function exportRatesOfInstructor()
    {
        $branch = auth()->user()->schoolAccountBranchType;

        $instructor= $this->userRepository->getSchoolBranchInstructors($branch->id, ["schoolInstructorSubjects.gradeClass"])->items();

        return Excel::download(new AverageInstructorsRatesExport($instructor), "instructors-rates.xls");
    }

    public function getView($id)
    {
        $branch = auth()->user()->schoolAccountBranchType;
        $instructor = User::query()->findOrFail($id);
        $data['row'] = $this->userRepository->getSchoolBranchInstructorRatings($branch->id,$id);
        $data['instructor'] = $instructor;
        $data['page_title'] = $this->title;
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function ExportInstructorRates(User $instructor)
    {
        $instructorRates = Rating::query()
            ->where("instructor_id", "=", $instructor->id)
            ->whereHas("user")
            ->get();

        $fileName = $instructor->name . "_" . count($instructorRates). ".xls";

        return Excel::download(new InstructorRatesExport($instructorRates, $this->exportHeadings()), $fileName);
    }

    /**
     * @return string[]
     */
    private function exportHeadings()
    {
        return [
            "Student Name",
            "Comment",
            "Rate",
            "Created On"
        ];
    }
}
