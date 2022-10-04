<?php

declare(strict_types=1);

namespace App\Listeners\Subject;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\Producers\Subject\SubjectCreatedSync;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class UpdateSubjectListener
{
    private $repository;
    private $payloadHandler;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(SubjectRepositoryInterface $subjectRepository, SubjectPayloadHandler $payloadHandler)
    {
        $this->repository = $subjectRepository;
        $this->payloadHandler = $payloadHandler;
    }


    public function handle(array $payload)
    {
        Log::info('UpdateSubjectListener', $payload);
        try {
            if (!empty($payload)) {

                $payloadHandler = $this->payloadHandler->handle($payload);

                $dataPrepared = [
                    'name' => $payload['name_en'] ?? $payload['name_ar'] ?? '',
                    'our_edu_reference' => $payload['our_edu_reference'],
                    'educational_system_id' => $payloadHandler['educational_system_id'],
                    'educational_term_id' => $payloadHandler['educational_term_id'],
                    'academical_years_id' => $payloadHandler['academic_year_id'],
                    'grade_class_id' => $payloadHandler['grade_class_id'],
                    'is_active' => 1
                ];

                if (is_null($payload['ta3lom_reference'])) {
                    $createdSubjectId = Subject::updateOrCreate([
                        'our_edu_reference' => $payload['our_edu_reference']
                    ], $dataPrepared);

                    SubjectCreatedSync::publish(
                        [
                            'our_edu_reference' => $payload['our_edu_reference'],
                            'ta3lom_reference' => $createdSubjectId
                        ]
                    );
                } else {
                    $this->repository->updateUsingModel($payload['ta3lom_reference'], $dataPrepared);
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
