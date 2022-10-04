<?php
namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Quizzes\Enums\QuizTimesEnum;
use App\OurEdu\Quizzes\Enums\QuizTypesEnum;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Transformers\ClassroomClassSessionsTransformer;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB;
class ClassroomClassController extends BaseApiController
{
    /**
     * @var TokenManagerInterface
     */
    private $tokenManager;

    /**
     * ClassroomClassController constructor.
     * @param TokenManagerInterface $tokenManager
     */
    public function __construct(TokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function getIndex(){

        $student = Auth::guard('api')->user()->student;
        $sessions = ClassroomClassSession::where('classroom_id' , $student->classroom_id)
//            ->whereDate('from' , '>=' , now())
            ->cursor();

        return $this->transformDataModInclude($sessions , 'subject,classroom' ,new ClassroomClassSessionsTransformer() , ResourceTypesEnums::CLASSROOM_CLASS_SESSION);
    }
    public function getTodaysClassRoomClasses(){
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
        $student = Auth::guard('api')->user()->student;
        $classrooms = [$student->classroom_id];
        if($student->specialClassroom) {
            $classrooms = $student->specialClassroom->pluck('id')->toArray();
            array_push($classrooms, $student->classroom_id);
        }

        $params['token'] = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);
        $sessions = VCRSession::query()
            ->whereIn('classroom_id', $classrooms)
            ->where("time_to_start",'>=',\Carbon\Carbon::today()->format('Y-m-d H:i:s'))
            ->where("time_to_start",'<',Carbon::tomorrow()->format('Y-m-d H:i:s'))
            ->with([
                'classroom','classroomClassSession','instructor','beforeSessionQuizzes','afterSessionQuizzes',
                'subject.educationalSystem','subject.academicalYears','subject.gradeClass'
            ])->orderBy('time_to_start','asc')
        ->get();

        return $this->transformDataModInclude($sessions, 'subject,classroom,instructor', new ClassroomClassSessionsTransformer($params), ResourceTypesEnums::CLASSROOM_CLASS_SESSION);
    }
}
