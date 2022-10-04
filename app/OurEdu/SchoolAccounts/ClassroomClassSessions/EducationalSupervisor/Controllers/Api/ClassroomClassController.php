<?php
namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\EducationalSupervisor\Controllers\Api;

use App\Exceptions\ErrorResponseException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\Classroom;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\EducationalSupervisor\Transformers\ClassroomClassSessionsTransformer;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Repositories\ClassroomClassSessionRepositoryInterface;
use App\OurEdu\GradeClasses\Repository\GradeClassRepositoryInterface;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Users\User;
use Illuminate\Support\Facades\Log;
use App\Exceptions\OurEduErrorException;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;

class ClassroomClassController extends BaseApiController
{
    /**
     * @var ClassroomRepositoryInterface
     */
    private $classroomRepository;
    /**
     * @var ClassroomClassSessionRepositoryInterface
     */
    private $classSessionRepository;

    private $gradeClassRepository;


    protected $filters;

    /**
     * ClassroomClassController constructor.
     * @param ClassroomRepositoryInterface $classroomRepository
     * @param ClassroomClassSessionRepositoryInterface $classSessionRepository
     */
     /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    /**
     * ClassroomClassController constructor.
     * @param TokenManagerInterface $tokenManager
     */
    public function __construct(
        ClassroomRepositoryInterface $classroomRepository,
        ClassroomClassSessionRepositoryInterface $classSessionRepository,
        GradeClassRepositoryInterface $gradeClassRepository,
        TokenManagerInterface $tokenManager
    )
    {
        $this->classroomRepository = $classroomRepository;
        $this->classSessionRepository = $classSessionRepository;
        $this->gradeClassRepository = $gradeClassRepository;
        $this->tokenManager = $tokenManager;
    }

    public function index()
    {
        try {
            if(auth()->user() && !auth()->user()->type == UserEnums::EDUCATIONAL_SUPERVISOR){
                throw new ErrorResponseException(trans("app.Unauthorized actions"));
            }
            if (!\auth()->user()->is_active) {

                $this->tokenManager->revokeAuthAllAccessTokens();
    
                formatErrorValidation(
                    [
                    'status' =>403,
                    'detail' => trans('auth.This account is suspended'),
                    'title' => trans('auth.This account is suspended')
                    ],
                    403
                );
            }

            $subjectId = request()->input('subject_id');
            $gradeClassId = request()->input('grade_class_id');
            $branchId = request()->input('branch_id');
            $classroom_id = request('classroom_id');
            if($subjectId && $gradeClassId && $branchId && $classroom_id) {
                $filters = [
                    'subject_id' => $subjectId,
                    'gradeClass' => $gradeClassId,
                    'branch_id' => $branchId,
                    'classroom_id' => $classroom_id
                ];
                $params['token'] = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);
                $sessions = $this->classSessionRepository->getSessions($filters);
                return $this->transformDataModInclude($sessions , 'subject,classroom' ,new ClassroomClassSessionsTransformer($params) , ResourceTypesEnums::CLASSROOM_CLASS_SESSION);
            }
            else{
                return formatErrorValidation([
                    'status' => 422,
                    'detail' => 'parameter error',
                    'title' => 'validation error'
                ],422);
            }
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }
}
