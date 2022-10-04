<?php

namespace App\OurEdu\GeneralQuizzes\Student\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\GeneralQuizzes\Repositories\GeneralQuizRepository\GeneralQuizStudentRepositoryInterface;
use App\OurEdu\GeneralQuizzes\Student\Transformers\GeneralQuizTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralQuizReports extends BaseApiController
{
    private GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository;

    /**
     * @param GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository
     */
    public function __construct(GeneralQuizStudentRepositoryInterface $generalQuizStudentRepository)
    {
        $this->generalQuizStudentRepository = $generalQuizStudentRepository;
    }
    public function resultReport(Request $request)
    {
        $data = $request->all();
        $studentQuizzes = $this->generalQuizStudentRepository->getGeneralQuizStudents(Auth::user(), $data);

        return $this->transformDataModInclude(
            $studentQuizzes,
            "subject",
            new GeneralQuizTransformer(),
            ResourceTypesEnums::GENERAL_QUIZ
        );
    }
}
