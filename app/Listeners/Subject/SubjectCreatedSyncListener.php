<?php

declare(strict_types=1);

namespace App\Listeners\Subject;

use App\OurEdu\Subjects\Repository\SubjectRepository;
use Illuminate\Support\Facades\Log;

class SubjectCreatedSyncListener
{
    private $repository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(SubjectRepository $subjectRepository)
    {
        $this->repository = $subjectRepository;
    }

    public function handle($payload)
    {
        try {
            if (!empty($payload)) {
                $subjectId  = $payload['ta3lom_reference'];
                $dataPrepared = [
                    'our_edu_reference' => $payload['our_edu_reference']
                ];
                $this->repository->updateUsingModel($subjectId,$dataPrepared);
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
