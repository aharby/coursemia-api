<?php

namespace App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Controllers;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\SchoolAccounts\ClassroomClassSessions\ClassroomClassSession;
use App\OurEdu\SchoolAccounts\SchoolAccountBranches\SchoolAccountBranch;
use App\OurEdu\SchoolAccounts\SchoolAccounts\SchoolAccount;
use App\OurEdu\SchoolAccounts\SessionPreparations\Repositories\SessionPreparationRepository\SessionPreparationRepositoryInterface;
use App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Requests\StoreSessionPreparationRequest;
use App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\SessionPreparationUseCase\SessionPreparationUseCaseInterface;
use App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers\LookUpTransformer;
use App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers\ClassSessionsTransformer;
use App\OurEdu\SchoolAccounts\SessionPreparations\SchoolInstructor\Transformers\PreparationMediaTransformer;
use App\OurEdu\Users\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class SessionPreparationsApiController extends BaseApiController
{

    /**
     * @var ParserInterface
     */
    private $parserInterface;
    /**
     * @var SessionPreparationRepositoryInterface
     */
    private $repository;
    /**
     * @var SessionPreparationUseCaseInterface
     */
    private $sessionPreparationUseCase;

    /**
     * SessionPreparationsApiController constructor.
     * @param SessionPreparationRepositoryInterface $repository
     * @param SessionPreparationUseCaseInterface $sessionPreparationUseCase
     * @param ParserInterface $parserInterface
     */
    public function __construct(
        SessionPreparationRepositoryInterface $repository,
        SessionPreparationUseCaseInterface    $sessionPreparationUseCase,
        ParserInterface                       $parserInterface
    )
    {
        $this->repository = $repository;
        $this->parserInterface = $parserInterface;
        $this->sessionPreparationUseCase = $sessionPreparationUseCase;
    }

    /**
     * @param StoreSessionPreparationRequest $sessionPreparationRequest
     * @param int $sessionId
     * @return array|array[]|JsonResponse
     */
    public function save(StoreSessionPreparationRequest $sessionPreparationRequest, int $sessionId)
    {
        $data = $sessionPreparationRequest->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        $published = $sessionPreparationRequest->get('publish') == 'true' ? true : false;
        $classSession = $this->sessionPreparationUseCase->save($data, $sessionId, $published);
        return $this->transformDataModInclude($classSession, 'preparation.media', new ClassSessionsTransformer(), ResourceTypesEnums::CLASSROOM_CLASS_SESSION);
    }

    public function getClassSessionPreparation(int $sessionId)
    {
        $classSession = ClassroomClassSession::where('instructor_id', auth()->id())->with('preparation.media')->findOrFail($sessionId);
        return $this->transformDataModInclude($classSession, 'preparation.media,actions', new ClassSessionsTransformer(), ResourceTypesEnums::CLASSROOM_CLASS_SESSION);
    }

    public function MediaLibrary(Request $request)
    {
        $user = Auth::user();

        $mediaLibrary = $this->repository->getInstructorMediaLibrary($user, $request);

        return $this->transformDataMod($mediaLibrary, new PreparationMediaTransformer(), ResourceTypesEnums::PREPARATION_MEDIA);
    }

    public function schoolMediaLibrary(Request $request)
    {
        $user = Auth::user();

        $schoolBranches = SchoolAccountBranch::with('translations')
            ->where('school_account_id', $user->branch->schoolAccount->id)
            ->when($request->filled("branch_id"), function (Builder $branch) use ($request) {
                $branch->where("id", "=", $request->get("branch_id"));
            })
            ->orderBy('id', 'DESC')
            ->pluck('id')->toArray();

        $mediaLibrary = $this->repository->getInstructorBranchesMediaLibrary($schoolBranches, $request);

        return $this->transformDataMod($mediaLibrary, new PreparationMediaTransformer(), ResourceTypesEnums::PREPARATION_MEDIA);
    }

    public function getClassSessionPreparationLookUps(int $sessionId, Request $request)
    {
        $data = (object)['dum' => 'data'];
        $param = $request->get('filter') ?? [];
        return $this->transformDataModInclude($data, "", new LookUpTransformer($param, $sessionId), ResourceTypesEnums::LOOKUP);
    }
}
