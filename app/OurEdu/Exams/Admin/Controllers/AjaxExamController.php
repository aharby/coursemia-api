<?php

namespace App\OurEdu\Exams\Admin\Controllers;

use App\OurEdu\BaseApp\Controllers\AjaxController;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\OurEdu\EducationalSystems\Repository\EducationalSystemRepositoryInterface;

class AjaxExamController extends AjaxController
{
    private $subjectRepository;
    protected $systemRepository;

    public function __construct(SubjectRepositoryInterface $subjectRepository, EducationalSystemRepositoryInterface $systemRepository)
    {
        parent::__construct();
        $this->subjectRepository = $subjectRepository;
        $this->systemRepository = $systemRepository;
    }

    public function countrySystems()
    {
        $systems = $this->systemRepository->pluckByCountryId(request('country_id'));
        
        return response()->json(
            [
                    'status' => '200',
                    'systems' => $systems ?? []
                ]
            );
    }

    public function systemSubjects()
    {
        $subjects = $this->subjectRepository->pluckSystemSubjects(request('educational_system_id'));

        return response()->json(
            [
                    'status' => '200',
                    'subjects' => $subjects ?? [],
                ]
            );
    }
}
