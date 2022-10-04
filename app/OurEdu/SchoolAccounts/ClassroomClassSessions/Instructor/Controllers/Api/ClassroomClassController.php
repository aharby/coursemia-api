<?php
namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\ClassroomSessionStudentsTransformer;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\ClassroomClassSessionsTransformer;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Requests\ScoreStudentResultsRequest;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Repositories\ClassroomClassSessionRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\UseCases\ClassroomClassSessionUseCaseInterface;
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

    private $classroomSessionUseCase;
    private $parserInterface;
    /**
     * ClassroomClassController constructor.
     * @param ClassroomRepositoryInterface $classroomRepository
     * @param ClassroomClassSessionRepositoryInterface $classSessionRepository
     * @param ClassroomClassSessionUseCaseInterface $classroomSessionUseCase
     * @param ParserInterface $parserInterface
     */
    public function __construct(
        ClassroomRepositoryInterface $classroomRepository,
        ClassroomClassSessionRepositoryInterface $classSessionRepository,
        ClassroomClassSessionUseCaseInterface $classroomSessionUseCase,
        ParserInterface $parserInterface
    )
    {
        $this->parserInterface = $parserInterface;
        $this->classroomRepository = $classroomRepository;
        $this->classSessionRepository = $classSessionRepository;
        $this->classroomSessionUseCase = $classroomSessionUseCase;
    }



    public function getIndex(){

        $instructor = Auth::guard('api')->user();
        $sessions = ClassroomClassSession::where('instructor_id' , $instructor->id)
//            ->whereDate('from' , '>=' , now())
            ->cursor();

        return $this->transformDataModInclude($sessions , 'subject,classroom' ,new ClassroomClassSessionsTransformer() , ResourceTypesEnums::CLASSROOM_CLASS_SESSION);
    }


    public function getSessionStudents($sessionId){
        $useCase  = $this->classroomSessionUseCase->getSessionStudents($sessionId);
        $params['sessionPreparationMedia'] = $useCase['sessionPreparationMedia'];
        $params['session'] = $useCase['session'];
        return $this->transformDataModInclude($useCase['students'],'sessionScore',new ClassroomSessionStudentsTransformer($params) , ResourceTypesEnums::CLASS_ROOM_CLASS_SESSION_STUDENTS);
    }  

    public function scoreStudentSessionResult($sessionId,$studentId,ScoreStudentResultsRequest $request){
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->classroomSessionUseCase->scoreStudentSessionResults($sessionId,$studentId,$data);
        if ($useCase['status'] == 200) {
            return response()->json([
                'meta' => [
                    'message' =>  $useCase['message']
                ]
            ]);
        } else {
            return formatErrorValidation($useCase);
        }
    }
}
