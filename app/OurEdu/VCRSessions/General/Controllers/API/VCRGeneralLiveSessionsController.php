<?php

namespace App\OurEdu\VCRSessions\General\Controllers\API;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseNotification\Jobs\CheckStudentAbsent;
use App\OurEdu\BaseNotification\Jobs\FinishVCRSessionJob;
use App\OurEdu\BaseNotification\Jobs\NotificationInstructorsJob;
use App\OurEdu\BaseNotification\Jobs\NotificationStudentsJob;
use App\OurEdu\BaseNotification\Jobs\NotifySupervisorAboutAbsentInstructor;
use App\OurEdu\BaseNotification\NotifierFactory\NotifierFactoryInterface;
use App\OurEdu\Courses\Repository\LiveSessionRepositoryInterface;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Repository\VCRSessionParticipantsRepositoryInterface;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRSessionsStatusEnum;
use App\OurEdu\VCRSessions\General\Requests\UploadRecordFile;
use App\OurEdu\VCRSessions\General\Requests\UploadVCRFileRequest;
use App\OurEdu\VCRSessions\General\Transformers\GetSessionDataTransformer;
use App\OurEdu\VCRSessions\General\Transformers\RecordedVcrSessionTransformer;
use App\OurEdu\VCRSessions\General\Transformers\VCRSessionMediaTransformer;
use App\OurEdu\VCRSessions\General\Transformers\VCRTransformer;
use App\OurEdu\VCRSessions\Jobs\SetRoomLimit;
use App\OurEdu\VCRSessions\Models\VcrSupport;
use App\OurEdu\VCRSessions\Repositories\ZoomHostRepositoryInterface;
use App\OurEdu\VCRSessions\Student\Middleware\Api\StudentJoinSessionMiddleware;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\GetVCRSessionUseCaseInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class VCRGeneralLiveSessionsController extends BaseApiController
{
    public $user;
    public $params;
    private $liveSessionRepository;
    private $getVCRSessionUseCase;
    /**
     * @var NotifierFactoryInterface
     */
    private $notifierFactory;
    /**
     * @var VCRSessionParticipantsRepositoryInterface
     */
    private $participantsRepository;
    /**
     * @var ParserInterface
     */
    private $parserInterface;
    private ZoomHostRepositoryInterface $zoomHostRepository;

    public function __construct(
        ParserInterface $parserInterface,
        LiveSessionRepositoryInterface $liveSessionRepository,
        GetVCRSessionUseCaseInterface $getVCRSessionUseCase,
        NotifierFactoryInterface $notifierFactory,
        VCRSessionParticipantsRepositoryInterface $participantsRepository,
        ZoomHostRepositoryInterface $zoomHostRepository,
        VCRSessionRepositoryInterface $VCRSessionRepo
    )
    {
        $this->params = [];
        $this->user = Auth::guard('api')->user();
        $this->middleware(StudentJoinSessionMiddleware::class)->only(['studentJoinSession']);

        //register vars
        $this->liveSessionRepository = $liveSessionRepository;
        $this->getVCRSessionUseCase = $getVCRSessionUseCase;
        $this->notifierFactory = $notifierFactory;
        $this->participantsRepository = $participantsRepository;
        $this->parserInterface = $parserInterface;
        $this->zoomHostRepository = $zoomHostRepository;
        $this->VCRSessionRepo = $VCRSessionRepo;
    }


    public function getVCRSession(Request $request, $sessionId)
    {
        $useCase = $this->getVCRSessionUseCase->getVCRSession($request, $sessionId);
        if ($useCase['status'] != 200) {
            return formatErrorValidation($useCase);
        }
        // adding share_link in student case
        if (isset($useCase['share_link'])) {
            $this->params = $useCase['share_link'];
        }
        $include = '';
        if ($this->user->type == UserEnums::STUDENT_TYPE) {
            $include = 'preSessionQuiz,afterSessionQuiz,eduSupervisorQuiz';
        }
        return $this->transformDataModInclude(
            $useCase['vcrSession'],
            $include,
            new GetSessionDataTransformer($this->params),
            ResourceTypesEnums::VCR_SESSION
        );
    }
    public function leaveVCRSession(Request $request,$sessionId){
        $useCase = $this->getVCRSessionUseCase->leaveSession($sessionId);
        if ($useCase['status'] != 200) {
            return formatErrorValidation($useCase);
        }

        return response()->json([
            'meta' => [
                'message' => trans('vcr.you left session')
            ]
        ]);
    }
    public function finishVCRSession(Request $request, $sessionId, $type)
    {
        $vcrSession = VCRSession::find($sessionId);
        $vcrSession->ended_at = now();
        $user = auth('api')->user();
        $finishLog = [
            'closed_from' => $request->has('is_instructor') ? 'endpoint_instructor' : 'endpoint',
            'closed_by' => $user->id,
        ];
        if ($user && $request->has('is_instructor') && $vcrSession->status != VCRSessionsStatusEnum::FINISHED) {
            if (in_array($user->type, [UserEnums::SCHOOL_INSTRUCTOR, UserEnums::INSTRUCTOR_TYPE])) {
                $vcrSession->is_ended_by_instructor = 1;
                $vcrSession->instructor_end_time = now();

                $zoomHost = $vcrSession->zoomHost;
                if ($zoomHost and $vcrSession->vcr_session_type == VCRSessionEnum::REQUESTED_LIVE_SESSION) {
                    $this->zoomHostRepository->freeUsedHost($vcrSession);
                    $vcrSession->status = VCRSessionsStatusEnum::FINISHED;
                }
            }

            $vcrSession->save();

//            agoraFinishSession($vcrSession);
            $vcrSession->finishLog()->create($finishLog);
            return response()->json([
                'meta' => [
                    'message' => trans('app.Session has been ended')
                ]
            ]);
        }

        return response()->json([
            'meta' => [
                'message' => trans('app.Something went wrong')
            ]
        ],422);


    }

    public function checkVcrFinished($sessionId)
    {
        $vcrSession = VCRSession::find($sessionId);

        return $this->transformDataMod($vcrSession, new VCRTransformer(), ResourceTypesEnums::VCR_SESSION);
    }

    public function startVCRSession(Request $request, $sessionId, $type)
    {
        $vcrSession = VCRSession::find($sessionId);
        $vcrSession->started_at = now();
        $vcrSession->status = VCRSessionsStatusEnum::STARTED;
        $vcrSession->save();
        return response()->json([
            'meta' => [
                'message' => trans('app.Session has been started')
            ]
        ]);
    }

    public function getImage(Request $request, $sessionId, $type)
    {


        return response()->json([
            'meta' => [
                'image' => imageProfileApi($this->user->profile_picture)
            ]
        ]);
    }

    public function uploadVCRFile(UploadVCRFileRequest $request, $sessionId)
    {
        $vcrSession = VCRSession::findOrFail($sessionId);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = $file->getClientOriginalName();
            $filePath = $sessionId . '/' . $name;
            if (Storage::disk('s3Recording')->put($filePath, file_get_contents($file))) {
                $sessionMedia = $vcrSession->media()->create([
                    'source_filename' => $file->getClientOriginalName(),
                    'filename' => $filePath,
                    'url' => Storage::disk('s3Recording')->url($filePath),
                    'mime_type' => $file->getClientMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                    'status' => 1
                ]);
                return response()->json([
                    'meta' => [
                        'url' => $sessionMedia->url
                    ]
                ]);
            } else {
                return formatErrorValidation([
                    'status' => 500,
                    'detail' => trans('api.something went wrong with storage'),
                    'title' => 'something went wrong with storage',
                ]);
            }
        }
    }

    public function uploadRecordFile(UploadRecordFile $request,$sessionId)
    {
        $vcrSession = VCRSession::where('classroom_session_id',$sessionId)->firstOrFail();
        $recordedSession = $vcrSession->recordedFile()->where('chunk_id',$request->get('chunkId'))->first();
        $file = $request->file('file');
        if (!$recordedSession){
            $recordedSession = $vcrSession->recordedFile()->create([
                'status' => 0,
                'mime_type' => $file->getClientMimeType(),
                'chunk_id' => $request->get('chunkId')
            ]);
        }
        $filePath = Str::slug($sessionId .'-'.$recordedSession->id.'-' . $vcrSession->subject_name);
        $path = storage_path('chunks/'.$filePath.$file->getClientOriginalName());
        File::append($path, $file->get());
        if ($request->has('is_last') && $request->boolean('is_last')) {
            $name = basename($path, '.part');
            $name = Str::snake($name);
            File::move($path, storage_path('chunks/'.$name));
            if (Storage::disk('s3Recording')->put($name, fopen(storage_path('chunks/'.$name),'r+'))) {
                File::delete(storage_path('chunks/'.$name));
                $recordedSession->update([
                    'source_filename' => $name,
                    'extension' => pathinfo(Storage::disk('s3Recording')->url($name), PATHINFO_EXTENSION),
                    'filename' => $name,
                    'url' => Storage::disk('s3Recording')->url($name),
                    'status' => 1
                ]);
                return $this->transformDataMod($recordedSession,new RecordedVcrSessionTransformer(),ResourceTypesEnums::VCR_RECORD);
            }
            return formatErrorValidation([
                'status' => 500,
                'detail' => trans('api.something went wrong with storage'),
                'title' => 'something went wrong with storage',
            ]);
        }
        return response()->json([
            'meta' => [
                'message' => 'uploaded'
            ]
        ]);
    }

    public function getRecordedVcrFiles($sessionId)
    {
        $vcrSession = VCRSession::where('classroom_session_id',$sessionId)->firstOrFail();

        $records = collect([]);

        if($vcrSession->show_record){
            $records = $vcrSession->recordedFile()->where('status',1)->get();
        }

        return $this->transformDataMod($records,new RecordedVcrSessionTransformer(),ResourceTypesEnums::VCR_RECORD);
    }

    public function getVCRFiles($sessionId)
    {
        $vcrSession = VCRSession::findOrFail($sessionId);
        return $this->transformDataModInclude(
            $vcrSession->media,
            '',
            new VCRSessionMediaTransformer(),
            ResourceTypesEnums::VCR_SESSION_MEDIA
        );
    }

    public function vcrSupport(Request $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $data = [
            'message' => $data->message ?? null,
            'user_id' => \auth()->id() ?? null,
            'school_account_branch_id' => \auth()->user()->branch_id ?? null,
            'session_info' => $data->session_info ?? null,
            'agora_log_id' => $data->agora_log_id ?? null
        ];
        VcrSupport::create($data);
        return response()->json([
            'meta' => [
                'message' => 'send successfully'
            ]
        ]);
    }

    public function zoomLogErrors(Request $request)
    {

        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        $data = [
            'date_time' => $data->date_time,
            'message' => $data->message,
        ];
        Log::channel('zoom-error-stack')->error($data['message'], $data);

        return response()->json([
            'meta' => [
                'message' => 'send successfully'
            ]
        ]);
    }

    public function testVcrNotification()
    {
        $schoolVCRSessions = VCRSession::with(['instructor','classroom'])
            ->whereNotNull('instructor_id')
            ->whereNotNull('subject_id')
            ->where('id',1189751)
            ->where('vcr_session_type', VCRSessionEnum::SCHOOL_SESSION)
            ->get();
        if (!$schoolVCRSessions->isEmpty()) {
            $this->schoolsVCRSessions($schoolVCRSessions);
        }
    }
    private function schoolsVCRSessions($schoolVCRSessions)
    {
        foreach ($schoolVCRSessions as $vcrSession) {
            $isSpecial = (bool)$vcrSession->classroom->is_special;
            $toBeNotifiedStudents = $this->VCRSessionRepo
                ->getUnNotifiedClassroomStudents($vcrSession->classroom_id,$isSpecial);
            for ($i = 0; $i<300;$i++) {
                $this->notifyStudents($toBeNotifiedStudents, $vcrSession);

                $this->notifyInstructor($vcrSession->instructor, $vcrSession);
            }
        }
    }

    private function notifyStudents($studentsUsers, $vcrSession)
    {
        NotificationStudentsJob::dispatch($studentsUsers, $vcrSession)->delay(now()->addMinutes(15));
    }

    private function notifyInstructor($sessionInstructor, $vcrSession)
    {
        NotificationInstructorsJob::dispatch($sessionInstructor, $vcrSession)->delay(now()->addMinutes(15));
    }


}
