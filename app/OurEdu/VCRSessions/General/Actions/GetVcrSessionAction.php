<?php


namespace App\OurEdu\VCRSessions\General\Actions;

use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrType\GetVcrType;

class GetVcrSessionAction
{
    private ?VCRSession $vcrSession;
    private array $errors = [];
    private bool $success;

    private GetVcrType $getVcrType;

    /**
     * GetVcrSession constructor.
     * @param VCRSession $vcrSession
     */
    public function __construct(VCRSession $vcrSession)
    {
        $this->vcrSession = $vcrSession;
        $this->success = true;
        $this->execute();
    }

    /**
     * @param array $error
     */
    private function setErrors(array $error): void
    {
        $this->success = false;
        $this->errors[] = $error;
    }

    public function execute(): void
    {
        $this->getVcrType = new GetVcrType($this->vcrSession);


        if (!$this->getVcrType->execute()) {
            $error = [
                'status' => 422,
                'title' => trans("VCRSessions.can't register you as a presence"),
                'detail' => "can't register you as a presence",
            ];

            $this->setErrors($error);
        }
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }


}
