<?php

namespace App\OurEdu\Courses\Instructor\Transformers;

use App\OurEdu\BaseApp\Api\Enums\APIActionsEnums;
use App\OurEdu\BaseApp\Api\Transformers\ActionTransformer;
use App\OurEdu\BaseApp\Enums\ResourceTypesEnums;
use App\OurEdu\BaseApp\Enums\S3Enums;
use App\OurEdu\Courses\Models\Course;
use App\OurEdu\Courses\Models\SubModels\CourseMedia;
use App\OurEdu\GarbageMedia\MediaEnums;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CourseMediaTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        "actions",
    ];

    public function __construct(public $course = null)
    {

    }

    public function transform(CourseMedia $media)
    {
        $array = [
            "id" => $media->id,
            'course_id' => (int)$media->course_id,
            'course_title' => (string)$media->course->name,
            'mime_type' => (string)$media->mime_type,
            'file_name' => (string)$media->source_filename,
            'url' => (string)(getImagePath(S3Enums::LARGE_PATH . $media->filename)),
            'extension' => (string)$media->extension,
            'active' => (bool)$media->active,
            'created_at' => Carbon::parse($media->created_at)->format('Y-m-d') . " | " . Carbon::parse($media->created_at)->format('H:i')
        ];

        return array_merge($array, MediaEnums::getTypeExtensionsIconDisplay($media->extension, true));
    }

    /**
     * @param Course $course
     */
    public function includeActions(CourseMedia $media)
    {
        $actions = [];

        $label = trans('app.Activate Media');
        if ($media->active) {
            $label = trans('app.InActivate Media');
        }

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.instructor.courses.change-media-status', ['media' => $media->id]),
            'label' => $label,
            'method' => 'GET',
            'key' => APIActionsEnums::CHANGE_MEDIA_STATUS
        ];

        if (isset($this->course) && $this->course->end_date >= now()->toDateString()) {

            $actions[] = [
                'endpoint_url' => buildScopeRoute('api.instructor.courses.attach-media', ['course' => $media->course?->id]),
                'label' => $label,
                'method' => 'POST',
                'key' => APIActionsEnums::ATTACH_MEDIA
            ];
        }

        $actions[] = [
            'endpoint_url' => buildScopeRoute('api.instructor.courses.detach-media', ['course' => $media->course?->id]),
            'label' => trans('app.detach-media'),
            'method' => 'POST',
            'key' => APIActionsEnums::DETACH_MEDIA
        ];

        return $this->collection($actions, new ActionTransformer(), ResourceTypesEnums::ACTION);
    }

}
