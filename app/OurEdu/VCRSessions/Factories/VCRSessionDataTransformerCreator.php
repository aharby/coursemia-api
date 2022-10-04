<?php


namespace App\OurEdu\VCRSessions\Factories;


use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\UseCases\VCRSessionUseCase\VCRSessionUseCase;
use App\OurEdu\VCRSchedules\VCRSessionEnum;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrTypeDataTransform\Agora\AgoraVcrTypeDataTransformer;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrTypeDataTransform\VcrTypeDataTransform;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrTypeDataTransform\Zoom\ZoomVcrTypeDataTransformer;

class VCRSessionDataTransformerCreator
{
    /**
     * @var VCRSession
     */
    private VCRSession $vCRSession;
    /**
     * @var bool
     */
    private bool $success;
    /**
     * @var array
     */
    private array $errors;
    /**
     * @var VcrTypeDataTransform|null
     */
    private ?VcrTypeDataTransform $dataTransformInstance;
    /**
     * @var VCRSessionUseCase
     */
    private VCRSessionUseCase $VCRSessionUseCase;

    /**
     * VCRSessionDataTransformerCreator constructor.
     * @param VCRSession $vCRSession
     */
    public function __construct(VCRSession $vCRSession)
    {
        $this->success = true;
        $this->vCRSession = $vCRSession;
        $this->VCRSessionUseCase = app(VCRSessionUseCase::class);
        $this->handle();
    }

    private function handle(): void
    {
        $provider = $this->vCRSession->meeting_type ?? $this->VCRSessionUseCase->getSessionMeetingProvider($this->vCRSession);

        switch ($provider) {
            case VCRProvidersEnum::AGORA:
                $this->dataTransformInstance = new AgoraVcrTypeDataTransformer($this->vCRSession);
                break;
            case VCRProvidersEnum::ZOOM:
                $this->dataTransformInstance = new ZoomVcrTypeDataTransformer($this->vCRSession);
                break;
            default:
                $this->setErrors([
                    'status' => 422,
                    'detail' => 'type not supported',
                    'title' => 'type not supported',
                ]);
        }
    }

    /**
     * @param array $errors
     */
    private function setErrors(array $errors): void
    {
        $this->success = false;
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return VcrTypeDataTransform|null
     */
    public function getDataTransformInstance(): ?VcrTypeDataTransform
    {
        return $this->dataTransformInstance;
    }
}
