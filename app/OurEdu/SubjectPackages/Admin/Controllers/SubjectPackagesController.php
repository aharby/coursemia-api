<?php


namespace App\OurEdu\SubjectPackages\Admin\Controllers;

use App\OurEdu\SubjectPackages\Middleware\CheckPackageUsageMiddleware;
use Illuminate\Http\Request;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\Countries\Repository\CountryRepositoryInterface;
use App\OurEdu\SubjectPackages\Repository\SubjectPackageRepository;
use App\OurEdu\SubjectPackages\Admin\Requests\SubjectPackageRequest;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;
use App\OurEdu\SubjectPackages\Repository\SubjectPackageRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use Illuminate\Support\Str;

class SubjectPackagesController extends BaseController
{
    private $module;
    private $title;
    private $parent;
    private $subjectPackageRepository;
    private $countryRepository;
    private $educationalSystemRepository;
    private $optionRepository;
    private $gradeClassRepository;
    private $subjectRepository;

    public function __construct(
        SubjectPackageRepositoryInterface $subjectPackageRepository,
        CountryRepositoryInterface $countryRepository,
        EducationalSystemRepositoryInterface $educationalSystemRepository,
        OptionRepositoryInterface $optionRepository,
        GradeClassRepositoryInterface $gradeClassRepository,
        SubjectRepositoryInterface $subjectRepository
    ) {
        $this->module = 'subjectPackages';
        $this->subjectPackageRepository = $subjectPackageRepository;
        $this->countryRepository = $countryRepository;
        $this->educationalSystemRepository = $educationalSystemRepository;
        $this->optionRepository = $optionRepository;
        $this->gradeClassRepository = $gradeClassRepository;
        $this->subjectRepository = $subjectRepository;
        $this->title = trans('subject_package.Subject Package');
        $this->parent = ParentEnum::ADMIN;
        $this->middleware(CheckPackageUsageMiddleware::class)->only('delete');
    }

    public function getIndex()
    {
        $data['rows'] = $this->subjectPackageRepository->paginate();
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.subjectPackages.get.index')];
        $data = array_merge($data, $this->lookup());

        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function postCreate(SubjectPackageRequest $request)
    {
        $data = $request->except(['_token', 'subjects']);

        if ($package = $this->subjectPackageRepository->create($data)) {
            if (!Str::contains($package->picture,'subject-packages/')) {
                $package->update(['picture' => 'subject-packages/'.$package->picture]);
            }
            $packageRepo = new SubjectPackageRepository($package);

            if (is_array($request->subjects) && count($request->subjects) > 0) {
                $packageRepo->attachSubjects($request->subjects);
            }
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.subjectPackages.get.index');
        } else {
            flash()->error(trans('app.Oopps Something is broken'));
            return redirect()->back();
        }
    }

    public function getEdit($id)
    {
        $data['row'] = $this->subjectPackageRepository->findOrFail($id);

        $packageRepo = new SubjectPackageRepository($data['row']);

        $data['selectedSubjects'] = $packageRepo->getSubjectsIds();
        $data['page_title'] = trans('app.Edit').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route('admin.subjectPackages.get.index')];

        $data = array_merge($data, $this->lookup());

        return view($this->parent.'.'.$this->module.'.edit', $data);
    }


    public function putEdit(SubjectPackageRequest $request, $id)
    {
        $subjects = $request->subjects ?? [];
        $row = $this->subjectPackageRepository->findOrFail($id);

        $packageRepo = new SubjectPackageRepository($row);

        if ($package = $packageRepo->update($request->all())) {
            if (!Str::contains($package->picture,'subject-packages/')) {
                $package->update(['picture' => 'subject-packages/'.$package->picture]);
            }
            if (is_array($subjects) && count($subjects) > 0) {
                $packageRepo->syncSubjects($subjects);
            }
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.subjectPackages.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->subjectPackageRepository->findOrFail($id);
        $data['page_title'] = trans('app.View').' '.$this->title;
        $data['breadcrumb'] = [$this->title => route('admin.subjectPackages.get.index')];
        return view($this->parent.'.'.$this->module.'.view', $data);
    }

    public function delete($id)
    {
        $package = $this->subjectPackageRepository->findOrFail($id);
        $packageRepo = new SubjectPackageRepository($package);

        if ($packageRepo->delete()) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.subjectPackages.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function lookup()
    {
        $data['countries'] = $this->countryRepository->pluck()->toArray();
        $data['educationalSystems'] = $this->educationalSystemRepository->pluck()->toArray();
        $data['gradeClasses'] = $this->gradeClassRepository->pluck();
        $data['academicalYears'] = $this->optionRepository->pluckByType(OptionsTypes::ACADEMIC_YEAR);
        $data['subjects'] = $this->subjectRepository->getPluckSubjectsToArray();
        return $data;
    }
}
