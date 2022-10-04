<?php
namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Controllers\Api\V2;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Transformers\ClassroomClassSessionsTransformer;
use App\OurEdu\Users\Auth\Enum\TokenNameEnum;
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
    public function __construct(
        TokenManagerInterface $tokenManager
    )
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

        $instructor = Auth::guard('api')->user();
        $sessions = VCRSession::query()
            ->where('instructor_id', $instructor->id)
            ->where("time_to_start", ">=", $from)
            ->where("time_to_end", "<=", $to)
            ->with([
                'classroom','classroomClassSession','subject.educationalSystem','subject.academicalYears','subject.gradeClass'
            ])
            ->get();

        $params['token'] = $this->tokenManager->createAuthToken(TokenNameEnum::DYNAMIC_lINKS_Token);

        return $this->transformDataModInclude($sessions, 'subject,classroom', new ClassroomClassSessionsTransformer($params), ResourceTypesEnums::CLASSROOM_CLASS_SESSION);
    }
}
