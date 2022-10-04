<?php

declare(strict_types=1);

namespace App\Listeners\Subject;

use App\OurEdu\Subjects\Models\Subject;
use App\OurEdu\Subjects\Repository\SubjectRepositoryInterface;
use App\Producers\Subject\SubjectCreatedSync;
use Illuminate\Support\Facades\Log;

class CreateSubjectListener
{
    private $repository;
    private $payloadHandler;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        SubjectRepositoryInterface $subjectRepository,
        SubjectPayloadHandler $payloadHandler
    ) {
        $this->repository = $subjectRepository;
        $this->payloadHandler = $payloadHandler;
    }

    public function handle(array $payload)
    {
        Log::info('CreateSubjectListener', $payload);
        try {
            if (!empty($payload)) {

                $payloadHandler = $this->payloadHandler->handle($payload);
                Log::info($payloadHandler);
                $dataPrepared = [
                    'name' => $payload['name_en'] ?? $payload['name_ar'] ?? '',
                    'our_edu_reference' => $payload['our_edu_reference'],
                    'country_id' => env('fall_back_country_id'),
                    'is_active' => 1,
                    'educational_system_id' => $payloadHandler['educational_system_id'],
                    'educational_term_id' => $payloadHandler['educational_term_id'],
                    'academical_years_id' => $payloadHandler['academic_year_id'],
                    'grade_class_id' => $payloadHandler['grade_class_id'],
                    'is_active' => 1
                ];
                if (is_null($payload['ta3lom_reference'])) {
                    $createdSubjectId = $this->repository->create($dataPrepared)->id;
                } else {
                    $createdSubjectId = Subject::firstOrCreate(['our_edu_reference' => $payload['our_edu_reference']], $dataPrepared)->id;
                }

                SubjectCreatedSync::publish(
                    [
                        'our_edu_reference' => $payload['our_edu_reference'],
                        'ta3lom_reference' => $createdSubjectId
                    ]
                );
                Log::info('subjectCreatedSync', [
                    'our_edu_reference' => $payload['our_edu_reference'],
                    'ta3lom_reference' => $createdSubjectId
                ]);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
