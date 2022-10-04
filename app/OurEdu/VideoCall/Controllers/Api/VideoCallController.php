<?php


namespace App\OurEdu\VideoCall\Controllers\Api;

use App\Events\VideoCallStatusEvent;
use App\Http\Controllers\Controller;
use App\OurEdu\BaseApp\Api\Traits\ApiResponser;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\VideoCall\Repositories\VideoCallRepositoryInterface;
use App\OurEdu\VideoCall\Requests\LeaveVideoCall;
use App\OurEdu\VideoCall\Requests\VideoCallCancelRequest;
use App\OurEdu\VideoCall\Requests\VideoCallRequest;
use App\OurEdu\VideoCall\Requests\VideoCallStatusRequest;
use App\OurEdu\VideoCall\UseCases\VideoCallUseCase\VideoCallUseCaseInterface;
use Illuminate\Http\Request;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\OurEdu\VideoCall\Transformers\VideoCallRequestTransformer;

class VideoCallController extends Controller
{
    use ApiResponser;

    private $videoCallRepository, $videoCallUseCase;
    /**
     * @var ParserInterface
     */
    private $parserInterface;

    public function __construct(
        VideoCallRepositoryInterface $videoCallRepository,
        VideoCallUseCaseInterface $videoCallUseCase,
        ParserInterface $parserInterface
    )
    {
        $this->videoCallRepository = $videoCallRepository;
        $this->videoCallUseCase = $videoCallUseCase;
        $this->parserInterface = $parserInterface;
    }

    public function videoCallRequest(VideoCallRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->videoCallUseCase->makeCallRequest($data);
        return $this->transformDataMod($useCase['video_call_request'], new VideoCallRequestTransformer(), ResourceTypesEnums::VIDEO_CALL);
    }

    public function updateVideoCallStatus(VideoCallStatusRequest $request): JsonResponse
    {
        $useCase = $this->videoCallUseCase->updateVideoCallStatus($request->all());
        return response()->json($useCase, 200);
    }

    public function cancelVideoCall(VideoCallCancelRequest $request)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->videoCallUseCase->cancelVideoCall($data);
        return $this->transformDataMod($useCase['video_call_request'], new VideoCallRequestTransformer(),ResourceTypesEnums::VIDEO_CALL);
    }

    public function LeaveVideoCall(LeaveVideoCall $request, User $user)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $useCase = $this->videoCallUseCase->LeaveVideoCall($data->video_call_request, $user);
        return response()->json($useCase, 200);
    }
}
