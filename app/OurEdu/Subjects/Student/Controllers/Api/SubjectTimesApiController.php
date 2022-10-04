<?php

namespace App\OurEdu\Subjects\Student\Controllers\Api;

use App\Exceptions\OurEduErrorException;
use App\OurEdu\BaseApp\Api\BaseApiController;
use App\OurEdu\ResourceSubjectFormats\Models\ResourceSubjectFormatSubject;
use App\OurEdu\Subjects\Enums\SubjectTimeEnum;
use App\OurEdu\Subjects\Models\SubModels\SubjectFormatSubject;
use App\OurEdu\Subjects\Models\SubModels\SubjectTime;
use App\OurEdu\Subjects\Student\Requests\SubjectTimeRequest;
use Illuminate\Support\Facades\Log;
use Swis\JsonApi\Client\Interfaces\ParserInterface;

class SubjectTimesApiController extends BaseApiController
{
    private $repository;
    private $parserInterface;

    public function __construct(
        ParserInterface $parserInterface
    )
    {
        $this->parserInterface = $parserInterface;
    }


    public function setSubjectTime(SubjectTimeRequest $request) {

        $data = $request->getContent();
        $data = $this->parserInterface->deserialize($data);
        $data = $data->getData();

        //TODO::to be moved to separate usecase
        try {
            $student = auth()->user()->student;
            $timable = null;
            $subject_id = null;
            switch ($data->timable_type) {
                case SubjectTimeEnum::SECTION :
                        $timable = SubjectFormatSubject::findOrFail($data->getId());
                        $timable->type = SubjectFormatSubject::class;
                        $subject_id = $timable->subject_id;
                    break;
                case SubjectTimeEnum::RESOURCE :
                        $timable = ResourceSubjectFormatSubject::findOrFail($data->getId());
                        $timable->type = ResourceSubjectFormatSubject::class;
                        $subject_id = $timable->subjectFormatSubject->subject_id;
                    break;

            }
            SubjectTime::create([
                'subject_id' => $subject_id,
                'student_id' => $student->id,
                'timable_type' => $timable->type ?? null,
                'timable_id' => $timable->id ?? null,
                'start_time' => now()->subSeconds($data->time),
                'time' => $data->time,
            ]);

            return response()->json([
                'meta' => [
                    'message' => trans('subject.time sent successfully')
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error($e);
            throw new OurEduErrorException($e->getMessage());
        }
    }
}
