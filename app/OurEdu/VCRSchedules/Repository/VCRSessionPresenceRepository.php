<?php

namespace App\OurEdu\VCRSchedules\Repository;

use App\OurEdu\BaseApp\Traits\Filterable;
use App\OurEdu\Users\Models\Student;
use App\OurEdu\Users\User;
use App\OurEdu\Users\UserEnums;
use App\OurEdu\VCRSchedules\Models\VCRRequest;
use App\OurEdu\VCRSchedules\Models\VCRSchedule;
use App\OurEdu\VCRSchedules\Models\VCRScheduleDays;
use App\OurEdu\VCRSchedules\Models\VCRSession;
use App\OurEdu\VCRSchedules\Models\VCRSessionParticipant;
use App\OurEdu\VCRSchedules\Models\VCRSessionPresence;
use Illuminate\Pagination\LengthAwarePaginator;

class VCRSessionPresenceRepository implements VCRSessionPresenceRepositoryInterface
{

    protected $model;

    public function __construct(VCRSessionPresence $VCRSessionPresence)
    {
        $this->model = $VCRSessionPresence;
    }
    public function create(array $data): VCRSessionPresence
    {
        return $this->model->create($data);
    }
}
