<?php


namespace App\OurEdu\VideoCall\Repositories;

use App\OurEdu\Users\User;

interface VideoCallRepositoryInterface
{
    public function create($user_student, $parent);
    public function updateVideoCallStatus($data);
    public function cancelVideoCall($data);
}
