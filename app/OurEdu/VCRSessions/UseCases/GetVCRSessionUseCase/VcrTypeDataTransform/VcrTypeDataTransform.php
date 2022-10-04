<?php
namespace App\OurEdu\VCRSessions\UseCases\GetVCRSessionUseCase\VcrTypeDataTransform;

use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

abstract class VcrTypeDataTransform
{
    protected VCRSession $vcrSession;
    protected ?Authenticatable $user;

    public function __construct(VCRSession $vcrSession)
    {
        $this->vcrSession = $vcrSession;
        $this->user = Auth::guard('api')->user();
    }

    public function getData(): array
    {
        $timeToStart = new Carbon(now());
        $timeToEnd = new Carbon($this->vcrSession->time_to_end);

        return [
            'id' => $this->vcrSession->id,
            'price' => $this->vcrSession->price . ' ' . trans('subject_packages.riyal'),
            'status' => $this->vcrSession->status,
            'student_id' => $this->vcrSession->student_id,
            'instructor_id' => $this->vcrSession->instructor_id,
            'subject_id' => (int) $this->vcrSession->subject_id,
            'vcr_request_id' => $this->vcrSession->vcr_request_id,
            'ended_at' => $this->vcrSession->ended_at,
            'time_to_start' => $this->vcrSession->time_to_start,
            'time_to_end' => $this->vcrSession->time_to_end,
            'time_to_end_in_seconds' => $timeToStart->diffInSeconds($timeToEnd) ?? '',
            'current_user_name' => Str::limit($this->user->name, 63, ''),
            'current_user_type' => $this->user->type,
            'meeting_type' => (string) $this->getMeetingType(),
        ];
    }

    public function includes(): string
    {
        return '';
    }

    /**
     * you have to return the meeting type that you extends this class for it like agora and zoom
     *
     * @return string
     */
    protected abstract function getMeetingType(): string;
}
