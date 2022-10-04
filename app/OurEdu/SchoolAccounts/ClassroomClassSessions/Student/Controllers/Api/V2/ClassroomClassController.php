<?php
namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Controllers\Api\V2;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Student\Transformers\ClassroomClassSessionsTransformer;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
use DB;
use App\OurEdu\Users\Auth\TokenManager\TokenManagerInterface;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function getIndex(Request $request)
    {
        $rules = [
            "from" => "nullable|date",
            "to" => "nullable|date",
        ];

        $this->validate($request, $rules);
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

        $from = $request->get("from", Carbon::today());

        if ($request->filled("from")) {
            $from = Carbon::parse($from);
        }

        if ($request->filled("to")) {
            $to = Carbon::parse($request->get("to"));
        } else {
            $to = $from->copy()->addDays(7);
        }

        $from = $from->format("Y-m-d 00:00:00");
        $to = $to->format("Y-m-d 23:59:00");
        $student = Auth::guard('api')->user()->student;

        $classrooms = [$student->classroom_id];
        if($student->specialClassroom) {
            $classrooms = $student->specialClassroom->pluck('id')->toArray();
            array_push($classrooms, $student->classroom_id);
        }


        $params['token'] = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);
        $sessions = VCRSession::query()
            ->whereIn('classroom_id', $classrooms)
            ->where("time_to_start", ">=", $from)
            ->where("time_to_end", "<=", $to)
            ->with([
                'classroom','classroomClassSession','instructor','beforeSessionQuizzes','afterSessionQuizzes',
                'subject.educationalSystem','subject.academicalYears','subject.gradeClass'
            ])
            ->get();


        return $this->transformDataModInclude($sessions, 'subject,classroom', new ClassroomClassSessionsTransformer($params), ResourceTypesEnums::CLASSROOM_CLASS_SESSION);
    }
}
