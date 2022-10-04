<?php


namespace App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrTypeDataTransform\Agora;


use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSessions\General\Enums\VCRProvidersEnum;
use App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrTypeDataTransform\VcrTypeDataTransform;
use Illuminate\Support\Str;

class AgoraVcrTypeDataTransformer extends VcrTypeDataTransform
{
    public function getData(): array
    {
        $data = parent::getData();
        $append = [];

        if (in_array($this->user->type, UserEnums::instructorsUsersTypes())) {
            $append['userUuid'] = $this->vcrSession->agora_instructor_uuid;
        } else {
            $append['userUuid'] = Str::uuid() . '-' . $this->user->id;
        }

        $append['current_user_role'] = (in_array($this->user->type, UserEnums::instructorsUsersTypes())) ? 1 : 2;
        $append['roomUuid'] = $this->vcrSession->room_uuid;
        $append['room_name'] = $this->vcrSession->subject_name;

        return array_merge($data, $append);
    }

    /**
     * you have to return the meeting type that you extends this class for it like agora and zoom
     *
     * @return string
     */
    protected function getMeetingType(): string
    {
        return VCRProvidersEnum::AGORA;
    }
}
