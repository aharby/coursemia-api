<?php

namespace App\Modules\Courses\Controllers\API;
use App\Http\Controllers\Controller;

use App\Enums\StatusCodesEnum;
use Illuminate\Http\Request;

use App\Modules\Courses\Repository\LectureRepository;
use Illuminate\Support\Facades\Auth;

class CourseLectureAPIController extends Controller
{
    protected $lectureRepository;

    public function __construct(LectureRepository $lectureRepository)
    {
        $this->lectureRepository = $lectureRepository;
    }

    public function saveLastPosition(Request $request)
    {
        $request->validate([
            'lecture_id' => 'required|exists:lectures,id',
            'last_position' => 'required|integer|min:0',
        ]);

        $user = Auth::user();

        if(!isset($user))
            return customResponse((object)[], trans('auth.User does not exist.'),422, StatusCodesEnum::FAILED);

        $this->lectureRepository->updateLastPosition($user->id, $request->lecture_id, $request->last_position);

        return customResponse((object)[], trans('api.progress_saved'),422, StatusCodesEnum::DONE);

    }

}
