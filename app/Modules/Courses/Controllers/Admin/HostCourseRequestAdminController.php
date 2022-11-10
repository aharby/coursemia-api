<?php

namespace App\Modules\Courses\Controllers\Admin;

use App\Enums\StatusCodesEnum;
use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\CourseNote;
use App\Modules\Courses\Repository\FlashCardRepositoryInterface;
use App\Modules\Courses\Repository\HostCourseRequestRepositoryInterface;
use App\Modules\Courses\Resources\Admin\HostCourseRequestsAdminResource;
use App\Modules\Courses\Resources\API\AdminCourseNoteResource;
use Illuminate\Http\Request;

class HostCourseRequestAdminController extends Controller
{
    public function __construct(
        public HostCourseRequestRepositoryInterface $hostCourseRequestRepository
    )
    {
    }

    public function index()
    {
        $hostCourseRequests = $this->hostCourseRequestRepository->all();

        return response()->json([
            'total' => $hostCourseRequests->total(),
            'hostCourseRequests' => HostCourseRequestsAdminResource::collection($hostCourseRequests->items())
        ]);
    }
}
