<?php

namespace App\OurEdu\VCRSchedules\Student\Controllers\Api;

use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use Swis\JsonApi\Client\Interfaces\ParserInterface;
use App\OurEdu\VCRSchedules\Student\Requests\RateVCRSessionRequest;
use App\OurEdu\VCRSchedules\Repository\VCRSessionRepositoryInterface;
use App\OurEdu\VCRSchedules\Student\Transformers\VCRSessionTransformer;
use App\OurEdu\VCRSchedules\Student\Middlewares\StudentVCRSessionMiddleware;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCaseInterface;

class VCRSessionController extends BaseApiController
{
    private $vCRSessionUseCase;
    protected $parserInterface;
    protected $vCRSessionRepository;

    public function __construct(
        VCRSessionUseCaseInterface $vCRSessionUseCase,
        ParserInterface $parserInterface,
        VCRSessionRepositoryInterface $vCRSessionRepository
    ) {
        $this->vCRSessionUseCase = $vCRSessionUseCase;
        $this->parserInterface = $parserInterface;
        $this->vCRSessionRepository = $vCRSessionRepository;

        $this->middleware('auth:api');
        $this->middleware('type:student');
        $this->middleware(StudentVCRSessionMiddleware::class);
    }

    public function rateVCRSession(RateVCRSessionRequest $request, $sessionId)
    {
        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();
        
        $vcrSession = $this->vCRSessionUseCase->rateVCRSession($data, $sessionId);

        $meta = [
            'message'   =>  trans('api.Thanks for rating')
        ];

        return $this->transformDataModInclude($vcrSession, ['ratings.user', 'ratings.instructor'], new VCRSessionTransformer(), ResourceTypesEnums::VCR_SESSION, $meta);
    }

    public function view($sessionId)
    {
        $vcrSession = $this->vCRSessionRepository->findOrFail($sessionId);

        return $this->transformDataModInclude($vcrSession, ['ratings.user', 'ratings.instructor'], new VCRSessionTransformer(), ResourceTypesEnums::VCR_SESSION);
    }
}
