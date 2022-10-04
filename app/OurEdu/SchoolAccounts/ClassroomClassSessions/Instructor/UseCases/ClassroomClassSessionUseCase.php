<?php

namespace App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\UseCases;


use Illuminate\Support\Facades\Auth;
use App\OurEdu\SchoolAccounts\Repositories\ClassroomRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Repositories\ClassroomClassSessionRepositoryInterface;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\Instructor\Models\ClassroomClassSessionScores;
use App\OurEdu\Users\User;
class ClassroomClassSessionUseCase implements ClassroomClassSessionUseCaseInterface
{
    private $classroomSessionRepo;

    public function __construct(
        ClassroomClassSessionRepositoryInterface $classroomSessionRepo
    ){
        $this->classroomSessionRepo = $classroomSessionRepo;
    }


    public function getSessionStudents($sessionId){
        $session = $this->classroomSessionRepo->findOrFail($sessionId);

        $students =
            User::query()
            ->where('is_active','=',true)
            ->whereHas('student',function($q) use($session){
                $q->where('classroom_id',$session->classroom_id);
            })
            ->with([
                'student',
                'preparationMedia',
                'userSessionScores'=>function($query) use ($session){
                    $query->where('classroom_session_id',$session->id);
                }
            ])->withCount(['VCRSessionsPresence'=>function($query) use ($session){
                    if($session->vcrSession)
                        $query->where('vcr_session_id',$session->vcrSession->id);
                }])
                ->get();
        $useCases['sessionPreparationMedia'] =
            $session->preparation && $session->preparation->media ?
                $session->preparation->media->pluck('id')->toArray():null;
        $useCases['session'] = $session;
        $useCases['students'] = $students;
        return $useCases;
    }

    public function scoreStudentSessionResults($sessionId,$studentId,$data){
        $session = $this->classroomSessionRepo->findOrFail($sessionId);
        $user = User::query()->findOrFail($studentId);
        $validationErrors = $this->validateSessionScoreResults($session,$user);
        if ($validationErrors) {
            return $validationErrors;
        }
        foreach($data->session_score as $score_data){
            ClassroomClassSessionScores::updateOrCreate(
                [
                    'student_id'=>$user->id,
                    'classroom_session_id'=>$session->id,
                    'score_type'=>$score_data->score_type
                ],
                [
                    'score'=>$score_data->score,
                    'classroom_id'=>$session->classroom_id
                ]
            );
        }

        $useCase['message'] = trans('classroomClassSession.Session Scores is set successfully');
        $useCase['status'] = 200;
        return $useCase;
    }

    private function validateSessionScoreResults($session,$user){
        $useCase=[];
        if($session->classroom_id != $user->student->classroom_id){
            $useCase['status'] = 422;
            $useCase['detail'] = trans('classroomClassSession.student not belong to this classroom');
            $useCase['title'] = 'student is not belong to session classroom';
            return $useCase;
        }

        if($session->to > now() && $session->vcrSession->status == VCRSessionsStatusEnum::FINISHED){
            $useCase['status'] = 422;
            $useCase['detail'] = trans('classroomClassSession.can not set session score,session not finished');
            $useCase['title'] = 'Can not set session score,session not finished yet';
            return $useCase;
        }
        return $useCase;
    }

}
