<?php

    namespace App\OurEdu\VCRSessions\Instructor\Controllers\API;

    use App\OurEdu\BaseApp\Api\BaseApiController;
    use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
    use App\OurEdu\VCRSchedules\Models\VCRSession;
    use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
    use App\OurEdu\VCRSessions\Instructor\Transformers\ListVCRSessionParticipantsTransformer;
    use App\OurEdu\VCRSessions\Instructor\Transformers\ListVCRSessionsTransformer;
    use App\OurEdu\VCRSessions\Instructor\Transformers\VCRSessionTransformer;
    use Illuminate\Support\Facades\Auth;

    class VCRServiceLiveSessionsController extends BaseApiController
    {
        public $user;
        private $vcrSessionRepo;

        public function __construct(VCRSessionRepositoryInterface $vcrSessionRepo)
        {
            $this->user = Auth::guard('api')->user();
            $this->vcrSessionRepo = $vcrSessionRepo;
        }

        public function listSessions()
        {
            $sessions = $this->vcrSessionRepo->getInstructorSessions($this->user->id);
            return $this->transformDataModInclude($sessions, '',
                new ListVCRSessionsTransformer(), ResourceTypesEnums::VCR_SESSION);
        }

        public function viewSession($sessionId)
        {
            $sessions = $this->vcrSessionRepo->findOrFail($sessionId);
            return $this->transformDataModInclude($sessions, '',
                new VCRSessionTransformer(), ResourceTypesEnums::VCR_SESSION);
        }

        public function listSessionParticipants($sessionId)
        {
            $session = $this->vcrSessionRepo->findOrFail($sessionId);
            $participants = $this->vcrSessionRepo->getSessionParticipants($session);
            return $this->transformDataModInclude($participants, '',
                new ListVCRSessionParticipantsTransformer(), ResourceTypesEnums::VCR_PARTICIPANT);
        }

        public function toggleShowRecords(VCRSession $vCRSession)
        {
            if($this->user->id !== $vCRSession->instructor_id){
                return abort(403);
            }
            $vCRSession->show_record = !$vCRSession->show_record;
            $vCRSession->save();

            return response()->json(['meta' => trans('api.Updated Successfully')]);
        }

    }
