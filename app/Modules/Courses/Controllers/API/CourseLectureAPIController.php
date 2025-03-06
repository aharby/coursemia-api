<?php

namespace App\Modules\Courses\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Modules\Courses\Repository\LectureRepository;

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

        $this->lectureRepository->updateLastPosition($request->lecture_id, $request->last_position);

        return response()->json(['message' => __('api.progress_saved')]);
    }

}
