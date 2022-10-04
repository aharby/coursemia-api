<?php
namespace App\OurEdu\SchoolAccounts\SchoolAccounts\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\BaseController;
use App\OurEdu\BaseApp\Enums\ParentEnum;
use App\OurEdu\Countries\Repository\CountryRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;
use App\OurEdu\Options\Enums\OptionsTypes;
use App\OurEdu\Options\Repository\OptionRepositoryInterface;
use App\OurEdu\SchoolAccounts\SchoolAccounts\Admin\Requests\SchoolAccountRequest;
use App\OurEdu\SchoolAccounts\SchoolAccounts\Admin\Requests\SchoolAccountUpdateRequest;
use App\OurEdu\SchoolAccounts\SchoolAccounts\Repository\SchoolAccountRepository;
use App\OurEdu\SchoolAccounts\SchoolAccounts\Admin\UseCases\SchoolAccountUseCases\SchoolAccountUseCaseInterface;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;

class SchoolAccountsController extends BaseController
{
    private $module;
    private $repository;
    private $title;
    private $parent;
    private $educationalSystemRepository;
    private $gradeClassRepository;
    private $countryRepository;
    private $optionRepository;
    /**
     * @var SchoolAccountUseCaseInterface
     */
    private $schoolAccountUseCase;

    public function __construct(
        SchoolAccountRepository $schoolAccountRepository,
        EducationalSystemRepositoryInterface $educationalSystemRepository,
        GradeClassRepositoryInterface $gradeClassRepository,
        CountryRepositoryInterface $countryRepository,
        OptionRepositoryInterface $optionRepository,
        SchoolAccountUseCaseInterface $schoolAccountUseCase
    )
    {
        $this->module = 'school_accounts';
        $this->title = trans('app.School Accounts');
        $this->repository = $schoolAccountRepository;
        $this->educationalSystemRepository = $educationalSystemRepository;
        $this->gradeClassRepository = $gradeClassRepository;
        $this->parent = ParentEnum::ADMIN;
        $this->countryRepository = $countryRepository;
        $this->optionRepository = $optionRepository;
        $this->schoolAccountUseCase = $schoolAccountUseCase;

    }

    public function getIndex()
    {

        $data['rows'] = $this->repository->allWith(['country','manager']);
        $data['page_title'] = $this->title;
        $data['breadcrumb'] = '';
        return view($this->parent . '.' . $this->module . '.index', $data);
    }

    public function getCreate()
    {
        $data['page_title'] = trans('app.Create') . ' ' . $this->title;
        $data['row'] = $this->repository;
        $data['breadcrumb'] = [$this->title => route('admin.school-accounts.get.index')];
        $data['meetingTypes'] = VCRProvidersEnum::getList();
        $data = array_merge($data, $this->lookup());

        return view($this->parent . '.' . $this->module . '.create', $data);
    }

    public function lookup()
    {

        $data['countries'] = $this->countryRepository->pluck()->toArray();
        $data['educationalTerms'] = $this->optionRepository->pluckByType(OptionsTypes::EDUCATIONAL_TERM);
        $data['academicalYears'] = $this->optionRepository->pluckByType(OptionsTypes::ACADEMIC_YEAR);

        return $data;
    }

    public function postCreate(SchoolAccountRequest $request)
    {
        $data = $request->except('_token');
        $schoolAccountResult = $this->schoolAccountUseCase->save($data);
        if (!is_null($schoolAccountResult['school_account'])) {
            flash()->success(trans('app.Created successfully'));
            return redirect()->route('admin.school-accounts.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getEdit($id)
    {
        $data['row'] = $this->repository->findWith($id, [
            'educationalSystems',
            'gradeClasses',
            'educationalTerms',
            'academicYears',
        ]);
        $data['page_title'] = trans('app.Edit') . ' ' . $this->title;
        $data['gradeClasses'] = $data['row']->gradeClasses->pluck('title', 'id');
        $data['educationalSystems'] = $data['row']->educationalSystems->pluck('name', 'id')->toArray();
        $data['breadcrumb'] = [$this->title => route('admin.school-accounts.get.index')];
        $data = array_merge($data, $this->lookup());
        return view($this->parent . '.' . $this->module . '.edit', $data);
    }


    public function putEdit(SchoolAccountUpdateRequest $request, $id)
    {
        if ($this->schoolAccountUseCase->update($request->all(), $id)) {
            flash()->success(trans('app.Update successfully'));
            return redirect()->route('admin.school-accounts.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

    public function getView($id)
    {
        $data['row'] = $this->repository->findWith($id, [
            'educationalSystems.translations',
            'gradeClasses.translations',
            'educationalTerms.translations',
            'academicYears.translations',
        ]);
        $data['page_title'] = trans('app.View') . ' ' . $this->title;
        $data['breadcrumb'] = [$this->title => route('admin.school-accounts.get.index')];
        return view($this->parent . '.' . $this->module . '.view', $data);
    }

    public function delete($id)
    {
        if ($this->repository->delete($id)) {
            flash()->success(trans('app.Deleted Successfully'));
            return redirect()->route('admin.school-accounts.get.index');
        }
        flash()->error(trans('app.Oopps Something is broken'));
        return redirect()->back();
    }

}
